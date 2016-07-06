using System;
using System.Collections;
using System.Collections.Generic;
using System.Drawing;
using System.IO;
using System.IO.Compression;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows.Forms;

namespace Captcha
{
    public class Validation
    {
        List<CharInfo> words_ = new List<CharInfo>();
        List<Bitmap> listbmp = new List<Bitmap>();
        public int iType { get; set; }

        public Validation()
        {
        }

        /// <summary>
        /// 加载样本
        /// </summary>
        public void LoadTrainSetFromDir(string dirname)
        {
            ArrayList Directorylst = GetDirectories(dirname);//获得所有文件夹
            foreach (DirectoryInfo dir in Directorylst)
            {
                Dictionary<string, Bitmap> ImgList = new Dictionary<string, Bitmap>();
                ImgList = GetImgs(dirname + dir.Name + "\\");//获取文件夹下的图片集
                foreach (string s in ImgList.Keys)
                {
                    var img = ImgList[s];//样本
                    int width = img.Width;
                    int height = img.Height;

                    bool[,] map = new bool[width, height];
                    for (int i = 0; i < width; i++)
                    {
                        for (int j = 0; j < height; j++)
                        {
                            var color = img.GetPixel(i, j);
                            map[i, j] = (color.R + color.G + color.B) < 500 ? true : false;
                        }
                    }
                    words_.Add(new CharInfo(Convert.ToChar(dir.Name.Substring(0, 1)), map));//存放样本及样本字母
                }
            }
        }

        /// <summary>
        /// 加载样本
        /// </summary>
        public void LoadTrainSet(string spath)
        {
            string BasePath = Path.GetDirectoryName(Application.StartupPath).Replace("bin", "");

            Dictionary<string, Bitmap> ImgList = new Dictionary<string, Bitmap>();
            ImgList = GetImgs(spath);//获取文件夹下的图片集
            foreach (string s in ImgList.Keys)
            {
                var img = ImgList[s];
                int width = img.Width;
                int height = img.Height;

                bool[,] map = new bool[width, height];
                for (int i = 0; i < width; i++)
                {
                    for (int j = 0; j < height; j++)
                    {
                        map[i, j] = (img.GetPixel(i, j).R + img.GetPixel(i, j).G + img.GetPixel(i, j).B) < 500 ? true : false;
                    }
                }
                words_.Add(new CharInfo(Convert.ToChar(s.Substring(0, 1)), map));
            }
        }


        /// <summary>
        /// 识别
        /// </summary>
        /// <param name="bmp"></param>
        /// <returns></returns>
        public string GetImageText(Bitmap bmp)
        {
            var result = string.Empty;
            var width = bmp.Width;//识别图片的宽
            var height = bmp.Height;//识别图片的高
            var table = ToTable(bmp);//识别图片信息，二维布尔数组
            var next = SearchNext(table, 8);//前8个空白

            while (next < width - 12)//后12个空白
            {
                var matched = Match(table, next);//从第一个黑色像素点开始匹配
                if (matched.Rate > 0.6)//匹配率大于60%
                {
                    result += matched.Char;
                    if(iType==0)
                        next = matched.X + 10;//匹配成功，向前推进一个字符的宽度
                    else
                        next = matched.X + 7;//匹配成功，向前推进一个字符的宽度
                }
                else
                {
                    next += 1;//匹配失败向前推进1像素
                }
            }

            return result;
        }

        /// <summary>
        /// 保存识别图片像素值
        /// </summary>
        /// <param name="bmp"></param>
        /// <returns></returns>
        private bool[,] ToTable(Bitmap bmp)
        {
            var table = new bool[bmp.Width, bmp.Height];
            for (int i = 0; i < bmp.Width; i++)
                for (int j = 0; j < bmp.Height; j++)
                {
                    var color = bmp.GetPixel(i, j);
                    table[i, j] = (color.R + color.G + color.B < 500);
                }
            return table;
        }

        /// <summary>
        /// 查找识别图片的开始像素点，返回第一个黑色像素点的X坐标
        /// </summary>
        /// <param name="table"></param>
        /// <param name="start"></param>
        /// <returns></returns>
        private int SearchNext(bool[,] table, int start)
        {
            var width = table.GetLength(0);
            var height = table.GetLength(1);
            for (start++; start < width; start++)//由左到右，从上到下
                for (int j = 0; j < height; j++)
                    if (table[start, j])//第一个黑色像素点
                        return start;

            return start;//返回第一个黑色像素点的X坐标
        }


        /// <summary>
        /// 匹配
        /// </summary>
        /// <param name="source"></param>
        /// <param name="start"></param>
        /// <returns></returns>
        private MatchedChar Match(bool[,] source, int start)
        {
            MatchedChar best = null;
            foreach (var info in words_)//循环样本
            {
                var matched = ScopeMatch(source, info.Table, start);
                matched.Char = info.Char;//样本字符
                if (best == null || best.Rate < matched.Rate)
                    best = matched;
            }
            return best;
        }

        /// <summary>
        /// 
        /// </summary>
        /// <param name="source"></param>
        /// <param name="target"></param>
        /// <param name="start"></param>
        /// <returns></returns>
        private MatchedChar ScopeMatch(bool[,] source, bool[,] target, int start)
        {
            int targetWidth = target.GetLength(0);
            int targetHeight = target.GetLength(1);
            int sourceWidth = source.GetLength(0);
            int sourceHeight = source.GetLength(1);

            double max = 0;
            var matched = new MatchedChar();
            for (int i = -2; i < 8; i++)//大约为1个字符的宽度，有重叠情况，向后轮回2个像素
                for (int j = -3; j < sourceHeight - targetHeight + 5; j++)
                {
                    double rate = FixedMatch(source, target, i + start, j);//识别图片与样本单像素比对
                    if (rate > max)
                    {
                        max = rate;
                        matched.X = i + start;
                        matched.Y = j;
                        matched.Rate = rate;
                    }
                }
            return matched;//返回匹配的字符信息
        }

        /// <summary>
        /// 识别图片与样本单像素比对，返回匹配率
        /// </summary>
        /// <param name="source"></param>
        /// <param name="target"></param>
        /// <param name="x0"></param>
        /// <param name="y0"></param>
        /// <returns></returns>
        private double FixedMatch(bool[,] source, bool[,] target, int x0, int y0)
        {
            double total = 0;//样本的总像素
            double count = 0;
            int targetWidth = target.GetLength(0);
            int targetHeight = target.GetLength(1);
            int sourceWidth = source.GetLength(0);
            int sourceHeight = source.GetLength(1);
            int x, y;

            for (int i = 0; i < targetWidth; i++)//样本宽度
            {
                x = i + x0;
                if (x < 0 || x >= sourceWidth)//大于识别图片的宽度则退出
                    continue;
                for (int j = 0; j < targetHeight; j++)//样本高度
                {
                    y = j + y0;
                    if (y < 0 || y >= sourceHeight)//大于识别图片的高度则退出
                        continue;

                    if (target[i, j])//以样本为准
                    {
                        total++;
                        if (source[x, y])
                            count++;//识别图片像素点与样本像素点都为真，则计分加1
                        else
                            count--;//识别图片像素点与样本像素点都不同，则计分减1
                    }
                    else if (source[x, y])
                        count -= 0.55;
                }
            }

            return count / total;
        }


        /// <summary>
        /// 
        /// </summary>
        private class CharInfo
        {
            public char Char { get; private set; }
            public bool[,] Table { get; private set; }

            public CharInfo(char ch, bool[,] table)
            {
                Char = ch;
                Table = table;
            }
        }

        /// <summary>
        /// 
        /// </summary>
        private class MatchedChar
        {
            public int X { get; set; }
            public int Y { get; set; }
            public char Char { get; set; }
            public double Rate { get; set; }
        }


        #region 获得文件夹
        /// <summary>
        /// 获得文件夹
        /// </summary>
        /// <param name="sPath"></param>
        /// <returns></returns>
        public ArrayList GetDirectories(string sPath)
        {
            ArrayList Directories = new ArrayList();
            DirectoryInfo dir = new DirectoryInfo(sPath);

            DirectoryInfo[] tmp = dir.GetDirectories();
            foreach (DirectoryInfo d in tmp)
            {
                Directories.Add(d);
            }
            return Directories;
        }
        #endregion

        #region 获得文件夹下的图片集
        /// <summary>
        /// 获得文件夹下的图片集
        /// </summary>
        /// <param name="sPath"></param>
        /// <returns></returns>
        public Dictionary<string, Bitmap> GetImgs(string sPath)
        {
            Dictionary<string, Bitmap> map = new Dictionary<string, Bitmap>();
            Bitmap bmp;

            DirectoryInfo dir = new DirectoryInfo(sPath);
            FileInfo[] tmp = dir.GetFiles();
            foreach (FileInfo fi in tmp)
            {
                bmp = new Bitmap(sPath + fi.Name);
                map.Add(fi.Name, bmp);
            }
            return map;
        }
        #endregion

    }
}
