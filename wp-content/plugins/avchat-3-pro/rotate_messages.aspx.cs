/** 
this script is called by the swf files every X seconds to obtain a message to show in the chat 
X is controlled trough the rotatingMessageTime option in avc_settings.xxx
the path to this script is specified trough the rotatingMessageUrl option in avc_settings.xxx
the message returned by this script can contain the following HTML tags:http://help.adobe.com/en_US/FlashPlatform/reference/actionscript/3/flash/text/TextField.html
1 variable is sent to this file via GET/query string:
the count variable is a number that contains the number of times the chat/user logged in the chat has executed this script, the 1st value is 1
**/

using System;


namespace AVChat
{
	 public partial class Rotate : System.Web.UI.Page
    {
		protected void Page_Load(object sender, EventArgs e)
        {
				string count = this.Request.Params["count"];
				string userSiteId = this.Request.Params["siteId"];
				string roomId = this.Request.Params["roomId"];
               
			   switch(Convert.ToInt32(count)%6){
				case 1:
					Response.Write("<font color='#009933' face='Courier New' size='11' >RM R0B0T: Hi, I am the <b>Rotating Messages R0B0T.</b></font>");
					break;
				case 2:
					Response.Write ("http://www.birds.com/wp-content/uploads/home/bird4.jpg");
					break;
				case 3:
					Response.Write("<font color='#009933' face='Courier New' size='11' >RM R0B0T: I can also show various chat rules like: DON'T SPAM.</font>");
					break;
				case 4:
					Response.Write("<font color='#009933' face='Courier New' size='11' ><b>RM R0B0T: I show up every X seconds to bring you thew news of the world.</b></font>");
					break;
				case 5:
					Response.Write ("<font color='#009933' face='Courier New' size='11' >RM R0B0T: By default this feature is enabled. YAY!</font>");
					break;
				case 0:
					Response.Write ("<font color='#009933' face='Courier New' size='11' >RM R0B0T: The <font color='#ff0000'> <b>styling</b></font> <font color='#0000ff'>of these messages</font> <font color='#ffff00'>can also<font><font color='#00ffff'> be <i>changed</i>.</font>");
					break;
			   }
			   
		}
	}
}


