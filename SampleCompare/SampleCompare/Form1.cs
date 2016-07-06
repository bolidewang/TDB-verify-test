using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Windows.Forms;
using System.IO;

namespace SampleCompare
{
    public partial class FinalDistinguish : Form
    {
        private List<Bitmap> Samples = new List<Bitmap>();
        private List<string> SampleNames = new List<string>();
        private int Threshold = 400;

        public FinalDistinguish()
        {
            InitializeComponent();

            this.openFileDialog1.InitialDirectory = AppDomain.CurrentDomain.BaseDirectory + "切片结果";
            this.tbThreshold.Text = this.Threshold.ToString();

            // 初始化样本集
            DirectoryInfo dirInfo = new DirectoryInfo(AppDomain.CurrentDomain.BaseDirectory + "样本");
            FileInfo[] fileinfos = dirInfo.GetFiles("*.jpg");
            for (int i = 0; i < fileinfos.Length; i++)
            {
                Samples.Add(new Bitmap(fileinfos[i].FullName));
                SampleNames.Add(fileinfos[i].Name.Substring(0, 1));
            }
        }

        private int isBlack(Color color)
        {
            if (color.R + color.G + color.B <= this.Threshold)
            {
                return 1;
            }
            return 0;
        }

        private int isWhite(Color color)
        {
            if (color.R + color.G + color.B > this.Threshold)
            {
                return 1;
            }
            return 0;
        }

        private void ClearForm()
        {
            this.pictureBox1.BackgroundImage = null;
            this.pictureBox2.BackgroundImage = null;
            this.pictureBox3.BackgroundImage = null;
            this.pictureBox4.BackgroundImage = null;

            this.lblDist1.Text = "";
            this.lblDist2.Text = "";
            this.lblDist3.Text = "";
            this.lblDist4.Text = "";
        }

        private void button1_Click(object sender, EventArgs e)
        {
            if (this.openFileDialog1.ShowDialog() == DialogResult.OK)
            {
                this.ClearForm();

                string[] files = this.openFileDialog1.FileNames;
                if (files.Length > 0)
                {
                    this.pictureBox1.BackgroundImage = Bitmap.FromFile(files[0]);
                    this.lblDist1.Text = this.Distinguish(new Bitmap(this.pictureBox1.BackgroundImage));
                }
                if (files.Length > 1)
                {
                    this.pictureBox2.BackgroundImage = Bitmap.FromFile(files[1]);
                    this.lblDist2.Text = this.Distinguish(new Bitmap(this.pictureBox2.BackgroundImage));
                }
                if (files.Length > 2)
                {
                    this.pictureBox3.BackgroundImage = Bitmap.FromFile(files[2]);
                    this.lblDist3.Text = this.Distinguish(new Bitmap(this.pictureBox3.BackgroundImage));
                }
                if (files.Length > 3)
                {
                    this.pictureBox4.BackgroundImage = Bitmap.FromFile(files[3]);
                    this.lblDist4.Text = this.Distinguish(new Bitmap(this.pictureBox4.BackgroundImage));
                }
            }
        }

        private string Distinguish(Bitmap sourceImg)
        {
            string ret = "";

            int width = sourceImg.Width;
            int height = sourceImg.Height;
            int min = width * height;

            for (int sampleIndex = 0; sampleIndex < Samples.Count; sampleIndex++)
            {
                int diffCount = 0;
                for (int xIndex = 0; xIndex < width; xIndex++)
                {
                    for (int yIndex = 0; yIndex < height; yIndex++)
                    {
                        try
                        {
                            if (isWhite(sourceImg.GetPixel(xIndex, yIndex)) != isWhite(Samples[sampleIndex].GetPixel(xIndex, yIndex)))
                            {
                                diffCount++;
                            }
                        }
                        catch (System.Exception ex)
                        {
                            diffCount++;
                        }

                        if (diffCount >= min)
                        {
                            break;
                        }
                    }

                    if (diffCount >= min)
                    {
                        break;
                    }
                }
                if (diffCount < min)
                {
                    min = diffCount;
                    ret = SampleNames[sampleIndex];
                }
            }

            return ret;
        }

        private void tbThreshold_KeyPress(object sender, KeyPressEventArgs e)
        {
            if (e.KeyChar == (char)Keys.Enter)
            {
                this.Threshold = Convert.ToInt32(this.tbThreshold.Text);
            }
            else if (!char.IsNumber(e.KeyChar) && e.KeyChar != (char)Keys.Back)
            {
                e.Handled = true;
            }
        }

        private void button1_Click_1(object sender, EventArgs e)
        {
            if (this.pictureBox1.BackgroundImage != null)
            {
                this.pictureBox5.BackgroundImage = this.BinaryPic(this.pictureBox1.BackgroundImage);
            }
            if (this.pictureBox2.BackgroundImage != null)
            {
                this.pictureBox6.BackgroundImage = this.BinaryPic(this.pictureBox2.BackgroundImage);
            }
            if (this.pictureBox3.BackgroundImage != null)
            {
                this.pictureBox7.BackgroundImage = this.BinaryPic(this.pictureBox3.BackgroundImage);
            }
            if (this.pictureBox4.BackgroundImage != null)
            {
                this.pictureBox8.BackgroundImage = this.BinaryPic(this.pictureBox4.BackgroundImage);
            }
        }

        private Bitmap BinaryPic(Image sourceImg)
        {
            Bitmap resultImg = new Bitmap(sourceImg);

            int width = resultImg.Width;
            int height = resultImg.Height;
            for (int xIndex = 0; xIndex < width; xIndex++)
            {
                for (int yIndex = 0; yIndex < height; yIndex++)
                {
                    try
                    {
                        if (isBlack(resultImg.GetPixel(xIndex, yIndex)) == 1)
                        {
                            resultImg.SetPixel(xIndex, yIndex, Color.Black);
                        }
                        else
                        {
                            resultImg.SetPixel(xIndex, yIndex, Color.White);
                        }
                    }
                    catch (System.Exception ex)
                    {
                        resultImg.SetPixel(xIndex, yIndex, Color.Black);
                    }
                }
            }

            return resultImg;
        }
    }
}
