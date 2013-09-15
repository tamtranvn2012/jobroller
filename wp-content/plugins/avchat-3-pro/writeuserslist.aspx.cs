/**
################ AVChat 3 Configuration file ####################
########################### .aspx Version ###############################
**/
using System;
using System.Collections.Generic;
using System.Web;
using System.IO;

namespace AVChat
{
    public partial class WriteUsersList : System.Web.UI.Page
    {
        private string ip = string.Empty;

        protected void Page_Load(object sender, EventArgs e)
        {
            string result = "&result=";
            if (!string.IsNullOrEmpty(ip) && (!string.IsNullOrEmpty(this.Request.UserHostAddress) && ip != this.Request.UserHostAddress))
            {
                this.Response.Close();
            }

            string instance = HttpContext.Current.Request.Params["instance"] ?? string.Empty;
            string sNumberOfUsers = HttpContext.Current.Request.Params["nr_of_users"] ?? string.Empty;
            int numberOfUsers;
            if (!int.TryParse(sNumberOfUsers, out numberOfUsers))
            {
                result += "Invalid number of users!";
                this.Response.Write(result);
                this.Response.Flush();
                this.Response.Close();
            }
            string xml = System.Web.HttpUtility.UrlDecode(HttpContext.Current.Request.Params["xml"] ?? string.Empty);
            string textfilename = HttpContext.Current.Server.MapPath("users_" + instance + ".txt");
            string xmlfilename = HttpContext.Current.Server.MapPath("users_" + instance + ".xml");

            if (instance.Length < 100 && numberOfUsers < 100000)
            {
                try
                {
                    TextWriter tw = new StreamWriter(textfilename);
                    tw.Write(numberOfUsers);
                    tw.Close();
                    result += string.Format("Success, wrote ({0}) to file ({1})", numberOfUsers, textfilename);
                }
                catch { result += (string.Format("Cannot write to file ({0})", textfilename)); }


                try
                {
                    TextWriter tw = new StreamWriter(xmlfilename);
                    tw.Write(xml);
                    tw.Close();
                    result += (string.Format("Success, wrote the xml to file ({0})", xmlfilename));
                }
                catch { result += (string.Format("Cannot write to file ({0})", xmlfilename)); }

                Response.Write(result);
            }
            else
            {
                Response.Write("instance=" + instance + ", nr_of_users=" + sNumberOfUsers);
                Response.Flush();
                Response.Close();
            }
        }
    }
}