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
    //this file is called by the media server (FMIS,Red5, Wowza) when a user connects to the media server
    //this file is called only when token authentication is turned on in the avchat30 app config file on the media server
    //a token var is sent via GET
    public partial class TokenVerify : System.Web.UI.Page
    {
        //ip: String
        //description: The IP of the FMS/Red5/Woowza server making calls to this script. This is a security feature so that this script is only called by your media server. 
        //values: any ip as string:"127.0.0.1" OR string.Empty for disabled
        //default: string.Empty
        private string ip = string.Empty;

        //storage: String
        //description: path from where to get the tokens, must be the same as in token_request.aspx.cs
        //values: any path to a existing or non existing folder
        //default: tokens folder in the web application root
        private string storage = HttpContext.Current.Server.MapPath("tokens");

        //cache_life: int
        //description: how many seconds after creation a token is considered valid
        //values: any number in seconds
        //default: 30
        private int cache_life = 30;

        protected void Page_Load(object sender, EventArgs e)
        {
            //if the ip var is set and the value is different than the ip of the caller we don't eecute anything
            if (!string.IsNullOrEmpty(ip) && (!string.IsNullOrEmpty(this.Request.UserHostAddress) && ip != this.Request.UserHostAddress))
            {
                this.Response.Close();
            }

            try
            {
                if (!string.IsNullOrEmpty(this.Request.Params["token"]))
                {
                    string token = this.Request.Params["token"];
                    if (!File.Exists(Path.Combine(storage, token)))
                    {
                        if (!string.IsNullOrEmpty(this.Request.Params["fms"]))
                        {
                            Response.Write("res=false");
                        }
                        else
                        {
                            Response.Write("false");
                        }
                        this.Response.Flush(); 
                        this.Response.Close();
                    }
                    else
                    {
                        StreamReader streamReader = new StreamReader(Path.Combine(storage, token));
                        string ttime = streamReader.ReadToEnd();
                        streamReader.Close();
                        double fileTime;
                        double.TryParse(ttime, out fileTime);
                        if ((DateTime.Now - new DateTime(1970, 1, 1, 0, 0, 0, 0).ToLocalTime()).TotalSeconds - fileTime >= cache_life)
                        {
                            File.Delete(Path.Combine(storage, token));
                            if (!string.IsNullOrEmpty(this.Request.Params["fms"]))
                            {
                                Response.Write("res=false");
                            }
                            else
                            {
                                Response.Write("false");
                            }
                            this.Response.Flush();
                            this.Response.Close();
                        }
                        else
                        {
                            if (!string.IsNullOrEmpty(this.Request.Params["fms"]))
                            {
                                Response.Write("res=true");
                            }
                            else
                            {
                                Response.Write("true");
                            }
                            this.Response.Flush();
                            this.Response.Close();
                        }
                    }
                }
                if (!string.IsNullOrEmpty(this.Request.Params["fms"]))
                {
                    Response.Write("res=false");
                }
                else
                {
                    Response.Write("false");
                }
                this.Response.Flush();
                this.Response.Close();
            }
            catch (Exception ex)
            {
                if (!string.IsNullOrEmpty(this.Request.Params["fms"]))
                {
                    Response.Write("res=false");
                }
                else
                {
                    Response.Write("false");
                }
                this.Response.Flush();
                this.Response.Close();
            }
        }
    }
}
