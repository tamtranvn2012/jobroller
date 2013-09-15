using System;

namespace AVChat
{
	 public partial class sendReport : System.Web.UI.Page
    {
		protected void Page_Load(object sender, EventArgs e)
        {
				string ip = Server.UrlDecode(this.Request.Params["ip"]);// the IP of the reported user
				string siteId = Server.UrlDecode(this.Request.Params["siteId"]);//the siteId of the reported user
				string username = Server.UrlDecode(this.Request.Params["username"]);//the username of the reported user
				string reason = Server.UrlDecode(this.Request.Params["reason"]);// the reason of the report
				string description = Server.UrlDecode(this.Request.Params["description"]);//additional description for the report (could be empty because it is optional)
				string roomId = Server.UrlDecode(this.Request.Params["roomId"]);//the id of the room from where the report was sent
				string roomName = Server.UrlDecode(this.Request.Params["roomName"]);// the name of the room from where the report was sent
				string reporter = Server.UrlDecode(this.Request.Params["reporter"]);// the name of the user who made the report
				string webCamSnapURL = Server.UrlDecode(this.Request.Params["webCamSnapURL"]);// the link to the web-cam snapshot of the reported user taken when the report was sent
				string textChatSnapURL = Server.UrlDecode(this.Request.Params["textChatSnapURL"]);// the link to the text-chat snapshot of the reported user taken when the report was sent
               
			   string result = "$result="+ip+" "+siteId+" "+username+" "+reason+" "+description+" "+roomId+" "+roomName+" "+reporter+" "+webCamSnapURL+" "+textChatSnapURL;


               // Example of mail sending 
               /*
               System.Net.Mail.MailMessage message = new System.Net.Mail.MailMessage();

               message.To.Add("webmaster@example.com");
               message.Subject = "User " + username + " with the ID " + siteId + " has been reported";

               message.From = new System.Net.Mail.MailAddress("webmaster@example.com");

               message.Body += "The following user has been reported " + Environment.NewLine;
               message.Body += "Username: "+username + Environment.NewLine;
               message.Body += "IP: "+ ip + Environment.NewLine;
               message.Body += "siteId: "+siteId + Environment.NewLine;
               message.Body +=  Environment.NewLine;
               message.Body += "You can view the report screenshots here:" + Environment.NewLine;
               message.Body += "Camera snapshot"+webCamSnapURL + Environment.NewLine;
               message.Body += "Text-chat snapshot"+textChatSnapURL + Environment.NewLine;
               message.Body += Environment.NewLine;
               message.Body += "You can also view a list of reported users at:"+ Environment.NewLine;
               message.Body += "AVChat admin area -> Reports panel" + Environment.NewLine;

               System.Net.Mail.SmtpClient smtp = new System.Net.Mail.SmtpClient("yoursmtphost");
               smtp.Send(message);
               */

			   Response.Write(result); 
		}
	}
}


