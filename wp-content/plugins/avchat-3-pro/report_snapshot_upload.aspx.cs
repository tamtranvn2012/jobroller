using System;
using System.Collections.Generic;
using System.Web;
using System.IO;
using System.Collections;

namespace AVChat
{
    public partial class reportSnapshot : System.Web.UI.Page
    {
        private const string UPLOAD_DIRECTORY = "report_snaps/";
        private IList extensions = new string[] { "jpg" };
        string response = string.Empty;

        public static void CopyStream(Stream input, Stream output)
        {
            byte[] buffer = new byte[8 * 1024];
            int len;
            while ((len = input.Read(buffer, 0, buffer.Length)) > 0)
            {
                output.Write(buffer, 0, len);
            }
        }

        protected void Page_Load(object sender, EventArgs e)
        {

            //this file is called by index.swf when a report is made
            string siteId = this.Request.Params["siteId"];//user's internal specified ID
            string type = this.Request.Params["type"];//snapshot type (text-chat snapshot or camera snapshot);

            //we make the report_snaps folder if it does not exists
            string uploadDirectory = HttpContext.Current.Server.MapPath(UPLOAD_DIRECTORY);
            if (!Directory.Exists(uploadDirectory))
            {
                Directory.CreateDirectory(uploadDirectory);
            }

            string image = Path.Combine(uploadDirectory, siteId + "_" + type);

            //Get the stream  
            Stream input = (Stream)Request.InputStream;

            using (Stream file = File.OpenWrite(image))
            {
                CopyStream(input, file);
                response = "?save=ok";
            }

           Response.Write(response);
        }
    }
}