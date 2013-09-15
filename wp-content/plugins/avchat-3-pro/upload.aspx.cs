/**
################ AVChat 3 Configuration file ####################
########################### .aspx Version ###############################
**/
using System;
using System.Collections.Generic;
using System.Web;
using System.IO;
using System.Collections;
using System.Web.UI;
using System.Web.UI.WebControls;
using System.Xml;

namespace AVChat
{
    public partial class Upload : System.Web.UI.Page
    {
        private const string UPLOAD_DIRECTORY = "uploadedFiles/";
        private List<string> extensions = new List<string>();
		
		
		protected void parseXml()
		{
			//Create the XmlDocument.
			XmlDocument doc = new XmlDocument();
			string fileName = Server.MapPath("file_types.xml");
			doc.Load(fileName);

			XmlNodeList elemList = doc.GetElementsByTagName("ext");
			for (int i = 0; i < elemList.Count; i++)
			{
				//Response.Write(elemList[i].InnerXml + "<br />");
				extensions.Add(elemList[i].InnerXml);
			}  
		}
		
		
        protected void Page_Load(object sender, EventArgs e)
        {
			
			parseXml();
		
            if (HttpContext.Current.Request.Files.Count > 0)
            {
                string uploaderUsername = this.Request.Params["uploaderUsername"];
                string uploaderSiteId = this.Request.Params["uploaderSiteId"];
                string destinationType = this.Request.Params["destinationType"]; //"user" when the file is sent to a user, "room" when the file is sent to a room
                string destUID = this.Request.Params["destUID"]; //the id of the user to which this file is sent
                string destRID = this.Request.Params["destRID"]; //the id of the room to which this file is sent OR the id of the room the user to which this file is destined is in
                string destUSN = this.Request.Params["destUSN"]; //the username of the user to which this file is sent or the name of the room to which the file is sent
                string SWFFolderURL = this.Request.Params["pathToSWFFolder"]; //the url to the folder that contains the swf as detected by the swf, without the http:// OR https:// part
                HttpPostedFile file = HttpContext.Current.Request.Files["Filedata"];

                string response = string.Empty;

                if (file != null && file.ContentLength > 0)
                {
                    //string filename = Path.GetFileName(file.FileName);
					string filename = uploaderUsername + "_" + Path.GetFileName(file.FileName);
                    string uploadDirectory = HttpContext.Current.Server.MapPath(UPLOAD_DIRECTORY);
                    if (!Directory.Exists(uploadDirectory))
                    {
                        Directory.CreateDirectory(uploadDirectory);
                    }
                    string uploadFolderURL = "http://" + SWFFolderURL + "/" + UPLOAD_DIRECTORY;
                    string uploadFile = Path.Combine(uploadDirectory, filename);

                    bool allowed = false;
                    if (extensions.Contains(Path.GetExtension(filename).TrimStart('.'))) allowed = true;

                    if (!allowed)
                    {
                        response = "?result=notallowed";
                    }
                    else
                    {
                        if (!File.Exists(uploadFile))
                        {
                            try
                            {
                                file.SaveAs(uploadFile);
                                response = "?result=success&fileurl=" + uploadFolderURL + (uploadFolderURL.EndsWith("/") ? string.Empty : "/") + filename;
                            }
                            catch
                            {
                                response = "?result=fail";
                            }
                        }
                        else
                        {
                            int diff = 1;
                            string baseFilename = Path.GetFileNameWithoutExtension(filename);
                            string baseExtension = Path.GetExtension(filename);
                            string currentFilename = Path.Combine(uploadDirectory, baseFilename + "-" + diff + baseExtension);
                            while (File.Exists(currentFilename))
                            {
                                currentFilename = Path.Combine(uploadDirectory, baseFilename + "-" + (++diff) + baseExtension);
                            }

                            try
                            {
                                file.SaveAs(currentFilename);
                                response = "?result=success&fileurl=" + uploadFolderURL + (uploadFolderURL.EndsWith("/") ? string.Empty : "/") + Path.GetFileName(currentFilename);
                            }
                            catch
                            {
                                response = "?result=fail";
                            }
                        }
                    }
                }
                Response.Write(response);
            }
        }
    }
}