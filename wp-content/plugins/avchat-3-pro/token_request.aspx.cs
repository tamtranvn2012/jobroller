using System;
using System.Collections.Generic;
using System.Web;
using System.Web.UI;
using System.Web.UI.WebControls;
using System.IO;
using System.Security.Cryptography;
using System.Text;
using System.Collections.Specialized;

namespace AVChat
{
    public partial class TokenRequest : System.Web.UI.Page
    {
        //storage: String
        //description: path from where to get the tokens, must be the same as in token_verify.aspx.cs
        //values: any path to a existing or non existing folder
        //default: tokens folder in the web application root
        private string storage = HttpContext.Current.Server.MapPath("tokens");

        //function to compute the md5 hash
        private string CalculateMD5(string input)
        {
            MD5 md5 = System.Security.Cryptography.MD5.Create();
            byte[] inputBytes = System.Text.Encoding.ASCII.GetBytes(input);
            byte[] hashBytes = md5.ComputeHash(inputBytes);

            StringBuilder sb = new StringBuilder();
            for (int i = 0; i < hashBytes.Length; i++)
            {
                sb.Append(hashBytes[i].ToString("x2"));
            }
            return sb.ToString();
        }

        protected void Page_Load(object sender, EventArgs e)
        {
            NameValueCollection HttpParameters = this.Request.Params;
            Response.Write("1=1&");

            try
            {
                //make the folder where to put the tokens
                if (!Directory.Exists(storage))
                {
                    Directory.CreateDirectory(storage);
                }

                //if the username is sent via POST
                if (!string.IsNullOrEmpty(HttpParameters["username"]))
                {
                    //we create a string that we consider the token
                    string token = CalculateMD5(this.Session.SessionID + HttpParameters["username"]);

                    //we write the token to disk
                    try
                    {
                        System.IO.StreamWriter file = new System.IO.StreamWriter(storage + Path.DirectorySeparatorChar + token);
                        file.Write(String.Format("{0:0}", (DateTime.Now - new DateTime(1970, 1, 1, 0, 0, 0, 0).ToLocalTime()).TotalSeconds));
                        file.Close();
                        Response.Write("token=" + token + "&writesuccess=true");
                    }
                    catch
                    {
                        Response.Write("token=" + token + "&writesuccess=false");
                    }

                    //we create a file called leanup_handle to help us decide when to clean up the OLD tokens
                    if (!File.Exists(storage + Path.DirectorySeparatorChar + "cleanup_handle"))
                    {
                        System.IO.StreamWriter file = new System.IO.StreamWriter(storage + Path.DirectorySeparatorChar + "cleanup_handle");
                        file.Close();
                    }

                    //if the cleanup handle is older than 300 seconds we start cleaning the tokens
                    if (File.Exists(storage + Path.DirectorySeparatorChar + "cleanup_handle") && DateTime.Now.Subtract(File.GetLastWriteTime(storage + Path.DirectorySeparatorChar + "cleanup_handle")).TotalSeconds > 300)
                    {
                        try
                        {
                            foreach (string filename in Directory.GetFiles(storage))
                            {
                                //we go trough the tokens in the token folder and remove any token older than 300 seconds
                                if (File.Exists(filename) && DateTime.Now.Subtract(File.GetLastWriteTime(filename)).TotalSeconds > 300)
                                {
                                    File.Delete(filename);
                                }
                            }
                        }
                        catch (Exception ex)
                        {
                            Response.Write("error=" + HttpUtility.UrlEncode(ex.Message));
                        }
                    }
                }
            }
            catch (Exception ex)
            {
                Response.Write("error=" + HttpUtility.UrlEncode(ex.Message));
            }
        }
    }
}
