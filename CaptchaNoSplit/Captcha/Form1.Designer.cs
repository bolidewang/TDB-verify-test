namespace Captcha
{
    partial class Form1
    {
        /// <summary>
        /// 必需的设计器变量。
        /// </summary>
        private System.ComponentModel.IContainer components = null;

        /// <summary>
        /// 清理所有正在使用的资源。
        /// </summary>
        /// <param name="disposing">如果应释放托管资源，为 true；否则为 false。</param>
        protected override void Dispose(bool disposing)
        {
            if (disposing && (components != null))
            {
                components.Dispose();
            }
            base.Dispose(disposing);
        }

        #region Windows 窗体设计器生成的代码

        /// <summary>
        /// 设计器支持所需的方法 - 不要
        /// 使用代码编辑器修改此方法的内容。
        /// </summary>
        private void InitializeComponent()
        {
            System.ComponentModel.ComponentResourceManager resources = new System.ComponentModel.ComponentResourceManager(typeof(Form1));
            this.txt_result = new System.Windows.Forms.TextBox();
            this.btn_simple = new System.Windows.Forms.Button();
            this.btn_complex = new System.Windows.Forms.Button();
            this.panelProcessing = new System.Windows.Forms.Label();
            this.pic_loading = new System.Windows.Forms.PictureBox();
            this.btn_gray = new System.Windows.Forms.Button();
            this.btn_bw = new System.Windows.Forms.Button();
            this.btn_recog = new System.Windows.Forms.Button();
            this.pictureBox1 = new System.Windows.Forms.PictureBox();
            ((System.ComponentModel.ISupportInitialize)(this.pic_loading)).BeginInit();
            ((System.ComponentModel.ISupportInitialize)(this.pictureBox1)).BeginInit();
            this.SuspendLayout();
            // 
            // txt_result
            // 
            this.txt_result.Location = new System.Drawing.Point(35, 127);
            this.txt_result.Multiline = true;
            this.txt_result.Name = "txt_result";
            this.txt_result.ScrollBars = System.Windows.Forms.ScrollBars.Vertical;
            this.txt_result.Size = new System.Drawing.Size(503, 342);
            this.txt_result.TabIndex = 1;
            // 
            // btn_simple
            // 
            this.btn_simple.Location = new System.Drawing.Point(35, 88);
            this.btn_simple.Name = "btn_simple";
            this.btn_simple.Size = new System.Drawing.Size(101, 23);
            this.btn_simple.TabIndex = 2;
            this.btn_simple.Text = "多张简单验证码";
            this.btn_simple.UseVisualStyleBackColor = true;
            this.btn_simple.Click += new System.EventHandler(this.btn_simple_Click);
            // 
            // btn_complex
            // 
            this.btn_complex.Location = new System.Drawing.Point(152, 88);
            this.btn_complex.Name = "btn_complex";
            this.btn_complex.Size = new System.Drawing.Size(102, 23);
            this.btn_complex.TabIndex = 3;
            this.btn_complex.Text = "多张复杂验证码";
            this.btn_complex.UseVisualStyleBackColor = true;
            this.btn_complex.Click += new System.EventHandler(this.btn_complex_Click);
            // 
            // panelProcessing
            // 
            this.panelProcessing.Location = new System.Drawing.Point(162, 213);
            this.panelProcessing.Name = "panelProcessing";
            this.panelProcessing.Size = new System.Drawing.Size(230, 78);
            this.panelProcessing.TabIndex = 4;
            this.panelProcessing.Text = "请稍等……";
            this.panelProcessing.TextAlign = System.Drawing.ContentAlignment.MiddleCenter;
            // 
            // pic_loading
            // 
            this.pic_loading.Image = ((System.Drawing.Image)(resources.GetObject("pic_loading.Image")));
            this.pic_loading.Location = new System.Drawing.Point(215, 242);
            this.pic_loading.Name = "pic_loading";
            this.pic_loading.Size = new System.Drawing.Size(22, 20);
            this.pic_loading.TabIndex = 5;
            this.pic_loading.TabStop = false;
            // 
            // btn_gray
            // 
            this.btn_gray.Location = new System.Drawing.Point(152, 23);
            this.btn_gray.Name = "btn_gray";
            this.btn_gray.Size = new System.Drawing.Size(75, 23);
            this.btn_gray.TabIndex = 6;
            this.btn_gray.Text = "灰度";
            this.btn_gray.UseVisualStyleBackColor = true;
            this.btn_gray.Click += new System.EventHandler(this.btn_gray_Click);
            // 
            // btn_bw
            // 
            this.btn_bw.Location = new System.Drawing.Point(244, 23);
            this.btn_bw.Name = "btn_bw";
            this.btn_bw.Size = new System.Drawing.Size(75, 23);
            this.btn_bw.TabIndex = 7;
            this.btn_bw.Text = "二值化";
            this.btn_bw.UseVisualStyleBackColor = true;
            this.btn_bw.Click += new System.EventHandler(this.btn_bw_Click);
            // 
            // btn_recog
            // 
            this.btn_recog.Location = new System.Drawing.Point(338, 23);
            this.btn_recog.Name = "btn_recog";
            this.btn_recog.Size = new System.Drawing.Size(75, 23);
            this.btn_recog.TabIndex = 8;
            this.btn_recog.Text = "识别";
            this.btn_recog.UseVisualStyleBackColor = true;
            this.btn_recog.Click += new System.EventHandler(this.btn_recog_Click);
            // 
            // pictureBox1
            // 
            this.pictureBox1.Location = new System.Drawing.Point(36, 23);
            this.pictureBox1.Name = "pictureBox1";
            this.pictureBox1.Size = new System.Drawing.Size(100, 33);
            this.pictureBox1.TabIndex = 9;
            this.pictureBox1.TabStop = false;
            // 
            // Form1
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 12F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.ClientSize = new System.Drawing.Size(585, 502);
            this.Controls.Add(this.pictureBox1);
            this.Controls.Add(this.btn_recog);
            this.Controls.Add(this.btn_bw);
            this.Controls.Add(this.btn_gray);
            this.Controls.Add(this.pic_loading);
            this.Controls.Add(this.panelProcessing);
            this.Controls.Add(this.btn_complex);
            this.Controls.Add(this.btn_simple);
            this.Controls.Add(this.txt_result);
            this.Name = "Form1";
            this.StartPosition = System.Windows.Forms.FormStartPosition.CenterScreen;
            this.Text = "Captcha";
            ((System.ComponentModel.ISupportInitialize)(this.pic_loading)).EndInit();
            ((System.ComponentModel.ISupportInitialize)(this.pictureBox1)).EndInit();
            this.ResumeLayout(false);
            this.PerformLayout();

        }

        #endregion

        private System.Windows.Forms.TextBox txt_result;
        private System.Windows.Forms.Button btn_simple;
        private System.Windows.Forms.Button btn_complex;
        private System.Windows.Forms.Label panelProcessing;
        private System.Windows.Forms.PictureBox pic_loading;
        private System.Windows.Forms.Button btn_gray;
        private System.Windows.Forms.Button btn_bw;
        private System.Windows.Forms.Button btn_recog;
        private System.Windows.Forms.PictureBox pictureBox1;
    }
}

