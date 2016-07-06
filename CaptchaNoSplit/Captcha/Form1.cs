using System;
using System.Collections;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Drawing.Imaging;
using System.IO;
using System.Linq;
using System.Text;
using System.Threading;
using System.Threading.Tasks;
using System.Windows.Forms;

namespace Captcha
{
    [System.Runtime.InteropServices.ComVisible(true)]
    public partial class Form1 : Form
    {
        Thread threadLoad;
        private static PixelFormat[] indexedPixelFormats = { PixelFormat.Undefined, PixelFormat.DontCare, PixelFormat.Format16bppArgb1555, PixelFormat.Format1bppIndexed, PixelFormat.Format4bppIndexed, PixelFormat.Format8bppIndexed }; 
        // 获取程序的基目录
        string BasePath = Path.GetDirectoryName(Application.StartupPath).Replace("bin", "");
        string sourcePath = "";
        string savePath = "";

        public Form1()
        {
            InitializeComponent();
            Control.CheckForIllegalCrossThreadCalls = false;
            this.pic_loading.Visible = false;
            this.panelProcessing.Visible = false;
            pictureBox1.Image = Image.FromFile(BasePath + "img\\bxgn.jpg");

        }

        /// <summary>
        /// 灰度化
        /// </summary>
        /// <param name="sender"></param>
        /// <param name="e"></param>
        private void btn_gray_Click(object sender, EventArgs e)
        {
            sourcePath = BasePath + "img\\";
            savePath = BasePath + "img_gray\\";
            ImgGray(sourcePath, savePath);//灰度化
            pictureBox1.Image = Image.FromFile(BasePath + "img_gray\\bxgn.jpg");

        }

        /// <summary>
        /// 二值化
        /// </summary>
        /// <param name="sender"></param>
        /// <param name="e"></param>
        private void btn_bw_Click(object sender, EventArgs e)
        {
            sourcePath = BasePath + "img\\";
            savePath = BasePath + "img_bw\\";
            ImgBlackWhite(sourcePath, savePath);//二值化
            pictureBox1.Image = Image.FromFile(BasePath + "img_bw\\bxgn.jpg");
        }

        /// <summary>
        /// 识别
        /// </summary>
        /// <param name="sender"></param>
        /// <param name="e"></param>
        private void btn_recog_Click(object sender, EventArgs e)
        {
            this.pic_loading.Visible = true;
            this.panelProcessing.Visible = true;
            txt_result.Text = "";
            threadLoad = new Thread(recog);
            threadLoad.SetApartmentState(ApartmentState.STA);
            threadLoad.Start();
        }


        /// <summary>
        /// 
        /// </summary>
        /// <param name="sender"></param>
        /// <param name="e"></param>
        private void btn_simple_Click(object sender, EventArgs e)
        {
            this.pic_loading.Visible = true;
            this.panelProcessing.Visible = true;
            txt_result.Text = "";
            threadLoad = new Thread(simple);
            threadLoad.SetApartmentState(ApartmentState.STA);
            threadLoad.Start();
        }

        /// <summary>
        /// 
        /// </summary>
        /// <param name="sender"></param>
        /// <param name="e"></param>
        private void btn_complex_Click(object sender, EventArgs e)
        {
            this.pic_loading.Visible = true;
            this.panelProcessing.Visible = true;
            txt_result.Text = "";
            threadLoad = new Thread(complex);
            threadLoad.SetApartmentState(ApartmentState.STA);
            threadLoad.Start();
        }


        /// <summary>
        /// 识别
        /// </summary>
        public void recog()
        {
            try
            {
                DateTime start = DateTime.Now;
                var validation = new Validation();
                validation.iType = 0;
                validation.LoadTrainSetFromDir(BasePath + "img\\yb2\\");

                Bitmap bmp = new Bitmap(BasePath + "img\\bxgn.jpg");
                var result = validation.GetImageText(bmp);

                DateTime end = DateTime.Now;
                TimeSpan ts = end - start;
                string t = (ts.Milliseconds + ts.Seconds * 1000 + ts.Minutes * 1000 * 60 + ts.Hours * 60 * 60 * 1000 + ts.Days * 24 * 60 * 60 * 1000).ToString();
                txt_result.Text = "结果：" + result + "\r\n用时：" + t.ToString() + " 毫秒\r\n";
            }
            finally
            {
                this.pic_loading.Visible = false;
                this.panelProcessing.Visible = false;
                if (threadLoad != null)
                {
                    threadLoad.Abort();//结束线程
                }
            }
        }


        /// <summary>
        /// 简单的多图识别
        /// </summary>
        public void simple()
        {
            try
            {
                DateTime start = DateTime.Now;

                var validation = new Validation();
                validation.iType = 0;
                validation.LoadTrainSet(BasePath + "test_train\\");
                string sResult = "";
                double Total = 0;
                double rightCount = 0;
                Dictionary<string, Bitmap> ImgList = new Dictionary<string, Bitmap>();
                ImgList = GetImgs(BasePath + "test_img\\");
                foreach (string s in ImgList.Keys)
                {
                    var result = validation.GetImageText(ImgList[s]);
                    sResult += BasePath + "test_img\\" + s + " --> " + result + "\r\n";
                    Total++;
                    if (s.Replace(".jpg", "") == result.ToLower())
                        rightCount++;
                }
                double rightRate = rightCount / Total;
                sResult += "\r\n识别：" + rightCount + "\r\n";
                sResult += "总数：" + Total + "\r\n";
                sResult += "正确率：" + rightRate * 100 + "%\r\n";

                DateTime end = DateTime.Now;
                TimeSpan ts = end - start;
                string t = (ts.Milliseconds + ts.Seconds * 1000 + ts.Minutes * 1000 * 60 + ts.Hours * 60 * 60 * 1000 + ts.Days * 24 * 60 * 60 * 1000).ToString();
                txt_result.Text = sResult + "用时：" + t.ToString() + " 毫秒\r\n";
            }
            finally
            {
                this.pic_loading.Visible = false;
                this.panelProcessing.Visible = false;
                if (threadLoad != null)
                {
                    threadLoad.Abort();//结束线程
                }
            }
        }


        /// <summary>
        /// 复杂的多图识别
        /// </summary>
        public void complex()
        {
            try
            {
                DateTime start = DateTime.Now;

                var validation = new Validation();
                validation.iType = 1;
                validation.LoadTrainSetFromDir(BasePath + "img\\yb\\");
                string sResult = "";
                double Total = 0;
                double rightCount = 0;
                Dictionary<string, Bitmap> ImgList = new Dictionary<string, Bitmap>();
                ImgList = GetImgs(BasePath + "img\\");
                foreach (string s in ImgList.Keys)
                {
                    var result = validation.GetImageText(ImgList[s]);
                    sResult += BasePath + "img\\" + s + " --> " + result + "\r\n";
                    Total++;
                    if (s.Replace(".jpg", "") == result.ToLower())
                        rightCount++;
                }
                double rightRate = rightCount / Total;

                sResult += "\r\n识别：" + rightCount + "\r\n";
                sResult += "总数：" + Total + "\r\n";
                sResult += "正确率：" + rightRate * 100 + "%\r\n";

                DateTime end = DateTime.Now;
                TimeSpan ts = end - start;
                string t = (ts.Milliseconds + ts.Seconds * 1000 + ts.Minutes * 1000 * 60 + ts.Hours * 60 * 60 * 1000 + ts.Days * 24 * 60 * 60 * 1000).ToString();
                txt_result.Text = sResult + "用时：" + t.ToString() + " 毫秒\r\n";
            }
            finally
            {
                this.pic_loading.Visible = false;
                this.panelProcessing.Visible = false;
                if (threadLoad != null)
                {
                    threadLoad.Abort();//结束线程
                }
            }
        }


        #region 灰度化
        /// <summary>
        /// 灰度化
        /// </summary>
        public void ImgGray(string sourcePath, string savePath)
        {
            ArrayList Filelst = GetFiles(sourcePath);
            foreach (FileInfo fn in Filelst)
            {
                string picname = fn.Name;
                if (!string.IsNullOrEmpty(picname))
                {
                    System.Drawing.Bitmap bmp = new System.Drawing.Bitmap(sourcePath + picname);
                    int Width = bmp.Width;
                    int Heigth = bmp.Height;

                    //如果原图片是索引像素格式，则需要转换
                    if (IsPixelFormatIndexed(bmp.PixelFormat))
                    {
                        //Bitmap imgtarget = bmp.Clone(new Rectangle(0, 0, bmp.Width, bmp.Height), PixelFormat.Format8bppIndexed);
                        Bitmap imgtarget = new Bitmap(bmp);
                        System.Drawing.Graphics gh = System.Drawing.Graphics.FromImage(imgtarget);
                        gh.InterpolationMode = System.Drawing.Drawing2D.InterpolationMode.HighQualityBicubic;
                        gh.SmoothingMode = System.Drawing.Drawing2D.SmoothingMode.HighQuality;
                        gh.CompositingQuality = System.Drawing.Drawing2D.CompositingQuality.HighQuality;
                        gh.DrawImage(imgtarget, 0, 0);
                        gh.Dispose();

                        for (int y = 0; y < Heigth; y++)
                        {
                            for (int x = 0; x < Width; x++)
                            {
                                System.Drawing.Color c = imgtarget.GetPixel(x, y);
                                int r = 0, g = 0, b = 0;
                                r = c.R;
                                g = c.G;
                                b = c.B;

                                //灰度
                                int rgb = (int)(r * 0.299 + g * 0.587 + b * 0.114);//加权平均值法
                                //int rgb = (int)((r + g + b) / 3.0);//平均值法
                                //int rgb = r > g ? r : g; rgb = rgb > b ? rgb : b;//最大值法
                                r = g = b = rgb;
                                imgtarget.SetPixel(x, y, System.Drawing.Color.FromArgb(r, g, b));
                            }
                        }

                        if (!Directory.Exists(savePath))
                        {
                            Directory.CreateDirectory(savePath);
                        }
                        imgtarget.Save(savePath + picname, System.Drawing.Imaging.ImageFormat.Jpeg);
                    }
                    else
                    {
                        for (int y = 0; y < Heigth; y++)
                        {
                            for (int x = 0; x < Width; x++)
                            {
                                System.Drawing.Color c = bmp.GetPixel(x, y);
                                int r = 0, g = 0, b = 0;
                                r = c.R;
                                g = c.G;
                                b = c.B;

                                //灰度
                                int rgb = (int)(r * 0.299 + g * 0.587 + b * 0.114);//加权平均值法
                                //int rgb = (int)((r + g + b) / 3.0);//平均值法
                                //int rgb = r > g ? r : g; rgb = rgb > b ? rgb : b;//最大值法
                                r = g = b = rgb;
                                bmp.SetPixel(x, y, System.Drawing.Color.FromArgb(r, g, b));
                            }
                        }

                        if (!Directory.Exists(savePath))
                        {
                            Directory.CreateDirectory(savePath);
                        }
                        if (!File.Exists(savePath + picname))
                            bmp.Save(savePath + picname, System.Drawing.Imaging.ImageFormat.Jpeg);
                    }
                }
            }
        }
        #endregion

        #region 二值化
        /// <summary>
        /// 二值化
        /// </summary>
        public void ImgBlackWhite(string sourcePath, string savePath)
        {
            ArrayList Filelst = GetFiles(sourcePath);
            foreach (FileInfo fn in Filelst)
            {
                string picname = fn.Name;
                if (!string.IsNullOrEmpty(picname))
                {
                    System.Drawing.Bitmap bmp = new System.Drawing.Bitmap(sourcePath + picname);
                    int Width = bmp.Width;
                    int Heigth = bmp.Height;

                    double Total = Width * Heigth;
                    double rgbSum = 0;

                    //如果原图片是索引像素格式，则需要转换
                    if (IsPixelFormatIndexed(bmp.PixelFormat))
                    {
                        Bitmap imgtarget = new Bitmap(bmp);
                        System.Drawing.Graphics gh = System.Drawing.Graphics.FromImage(imgtarget);
                        gh.InterpolationMode = System.Drawing.Drawing2D.InterpolationMode.HighQualityBicubic;
                        gh.SmoothingMode = System.Drawing.Drawing2D.SmoothingMode.HighQuality;
                        gh.CompositingQuality = System.Drawing.Drawing2D.CompositingQuality.HighQuality;
                        gh.DrawImage(imgtarget, 0, 0);
                        gh.Dispose();

                        //求阀值
                        for (int y = 0; y < Heigth; y++)
                        {
                            for (int x = 0; x < Width; x++)
                            {
                                System.Drawing.Color c = imgtarget.GetPixel(x, y);
                                int r = 0, g = 0, b = 0;
                                r = c.R;
                                g = c.G;
                                b = c.B;
                                int rgb = (int)(r * 0.299 + g * 0.587 + b * 0.114);//加权平均值法
                                rgbSum += rgb;
                            }
                        }
                        int fz = (int)(rgbSum / Total);

                        //二值化
                        for (int y = 0; y < Heigth; y++)
                        {
                            for (int x = 0; x < Width; x++)
                            {
                                System.Drawing.Color c = imgtarget.GetPixel(x, y);
                                int r = 0, g = 0, b = 0;
                                r = c.R;
                                g = c.G;
                                b = c.B;

                                //黑白
                                int rgbvalue = 0;
                                int rgb = (int)(r * 0.299 + g * 0.587 + b * 0.114);//加权平均值法
                                if (r+g+b > 500)//rgb>fz
                                    rgbvalue = 255;
                                else
                                    rgbvalue = 0;

                                r = g = b = (int)(rgbvalue);
                                imgtarget.SetPixel(x, y, System.Drawing.Color.FromArgb(r, g, b));
                            }
                        }

                        if (!Directory.Exists(savePath))
                        {
                            Directory.CreateDirectory(savePath);
                        }
                        if (!File.Exists(savePath + picname)) 
                            imgtarget.Save(savePath + picname, System.Drawing.Imaging.ImageFormat.Jpeg);
                    }
                    else
                    {
                        //求阀值
                        for (int y = 0; y < Heigth; y++)
                        {
                            for (int x = 0; x < Width; x++)
                            {
                                System.Drawing.Color c = bmp.GetPixel(x, y);
                                int r = 0, g = 0, b = 0;
                                r = c.R;
                                g = c.G;
                                b = c.B;
                                int rgb = (int)(r * 0.299 + g * 0.587 + b * 0.114);//加权平均值法
                                rgbSum += rgb;
                            }
                        }
                        int fz = (int)(rgbSum / Total);

                        //二值化
                        for (int y = 0; y < Heigth; y++)
                        {
                            for (int x = 0; x < Width; x++)
                            {
                                System.Drawing.Color c = bmp.GetPixel(x, y);
                                int r = 0, g = 0, b = 0;
                                r = c.R;
                                g = c.G;
                                b = c.B;

                                //黑白
                                int rgbvalue = 0;
                                int rgb = (int)(r * 0.299 + g * 0.587 + b * 0.114);//加权平均值法
                                if (r+g+b > 500)//rgb>fz
                                    rgbvalue = 255;
                                else
                                    rgbvalue = 0;

                                r = g = b = (int)(rgbvalue);
                                bmp.SetPixel(x, y, System.Drawing.Color.FromArgb(r, g, b));
                            }
                        }

                        if (!Directory.Exists(savePath))
                        {
                            Directory.CreateDirectory(savePath);
                        }
                        if (!File.Exists(savePath + picname))
                            bmp.Save(savePath + picname, System.Drawing.Imaging.ImageFormat.Jpeg);
                    }
                }
            }
        }
        #endregion

        #region 判断图片的PixelFormat是否含索引
        /// <summary>
        /// 判断图片的PixelFormat 是否在 引发异常的 PixelFormat 之中
        /// 无法从带有索引像素格式的图像创建graphics对象
        /// </summary>
        /// <param name="imgPixelFormat">原图片的PixelFormat</param>
        /// <returns></returns>
        private static bool IsPixelFormatIndexed(PixelFormat imgPixelFormat)
        {
            foreach (PixelFormat pf in indexedPixelFormats)
            {
                if (pf.Equals(imgPixelFormat)) return true;
            }

            return false;
        }
        #endregion


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

        #region 获得文件夹下的文件
        /// <summary>
        /// 获得文件夹下的文件
        /// </summary>
        /// <param name="sPath"></param>
        /// <returns></returns>
        public ArrayList GetFiles(string sPath)
        {
            ArrayList Files = new ArrayList();
            DirectoryInfo dir = new DirectoryInfo(sPath);

            FileInfo[] tmp = dir.GetFiles();
            foreach (FileInfo fi in tmp)
            {
                Files.Add(fi);
            }
            return Files;
        }
        #endregion

        #region 获得文件夹下的图片集
        /// <summary>
        /// 获得文件夹下的图片集
        /// </summary>
        /// <param name="sPath"></param>
        /// <returns></returns>
        public Dictionary<string,Bitmap> GetImgs(string sPath)
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
