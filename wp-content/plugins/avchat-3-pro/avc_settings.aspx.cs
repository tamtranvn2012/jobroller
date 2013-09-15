/**
################ AVChat 3.0 Configuration file ####################
########################### .aspx Version ######################### 
#### See avc_settings.php for explanation of each variable ########
#### 2 variables are sent via GET to this script:
####   admin=true (when this script is executed by admin.swf)
####   userId=XXX (This variable is sent to index.swf and admin.swf via GET and forwarded to this script. To edit it's value look in index.html and admin.html respectively.)
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
    public partial class GeneralSettings : System.Web.UI.Page
    {
        private static Dictionary<string, object> configurationData = new Dictionary<string, object>();

        private void CreateConfig()
        {
            configurationData = new Dictionary<string, object>();
			
			parseXml();

        }
		
		protected void parseXml()
		{
			//Create the XmlDocument.
			XmlDocument xmlDoc = new XmlDocument();
			string fileName = Server.MapPath("avc_settings.xml");
			
			
			if (File.Exists(fileName))
			{
				xmlDoc.Load(fileName);
				XmlNodeList settings = xmlDoc.GetElementsByTagName("title");
				XmlNodeList values = xmlDoc.GetElementsByTagName("value");
				
				for (int i = 0; i < settings.Count; i++){
					//Response.Write(settings[i].InnerText+": "+values[i].InnerText+"<br/>");
					configurationData.Add(settings[i].InnerText.ToString(), values[i].InnerText.ToString());
				}
					
			}
			else
				Response.Write("The file {0} could not be located"+fileName);
			
		} 

        protected void Page_Load(object sender, EventArgs e)
        {
            CreateConfig();
			
			//Setting the unique identifier for the user, default we use the users IP adress.
			configurationData["clientUniqueIdentifier"]=Request.ServerVariables["REMOTE_ADDR"];
			
			// The line below is needed by flash player
            Response.Write("a=b");
            foreach (KeyValuePair<string, object> configOption in configurationData)
            {
                Response.Write("&" + configOption.Key + "=" + HttpUtility.UrlEncode(configOption.Value.ToString()));
            }
        }
    }
}