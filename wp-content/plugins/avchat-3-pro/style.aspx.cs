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
    public partial class Style : System.Web.UI.Page
    {
		protected void parseXml()
		{
			//Create the XmlDocument.
			XmlDocument xmlDoc = new XmlDocument();
			string fileName = Server.MapPath("style.xml");
			xmlDoc.Load(fileName);

			if (File.Exists(fileName)){
			
				xmlDoc.Load(fileName);
				XmlNodeList nl = xmlDoc.SelectNodes("styles");
				XmlNode root = nl[0];

				foreach (XmlNode xnode in root.ChildNodes){
				
					//List<string> values = new List<string>();
					
					Response.Write("."+xnode.Name+"{\n");
					foreach(XmlNode tag in xnode.ChildNodes){
							Response.Write("\t"+tag.Name+":"+tag.InnerText+";\n");
					}
					Response.Write("}\n");
				}
					
			}
			else
				Response.Write("The file {0} could not be located"+fileName); 
		}
		
        protected void Page_Load(object sender, EventArgs e){
		
			parseXml();	
        }
    }
}

