using System;

namespace AVChat
{
    public partial class sendGift : System.Web.UI.Page
    {
        protected string destinationSiteId;
        protected string destinationUsername;
        protected string senderSiteId;
        protected string senderUsername;

        protected void Page_Load(object sender, EventArgs e)
        {
            ////By default AVChat 3 will send to this PHP file the following variables

            destinationSiteId = this.Request.Params["destinationSiteId"];
            destinationUsername = this.Request.Params["destinationUsername"];
            senderSiteId = this.Request.Params["senderSiteId"];
            senderUsername = this.Request.Params["senderUsername"];

            ////like this:sendgift.php?destinationSiteId=abc&destinationUsername=Nike&senderSiteId=123senderUsername=Nike
           
        }
    }
}


