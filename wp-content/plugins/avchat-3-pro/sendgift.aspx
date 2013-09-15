<%@ Page Language="C#" Debug="true" CodeFile="sendgift.aspx.cs" Inherits="AVChat.sendGift"
    AutoEventWireup="true" %>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title runat="server">Send a gift to
        <%=destinationUsername%></title>
    <style type="text/css">
        body
        {
            background-color: #ffffff;
            font-family: Arial,Verdana, "Times New Roman" ,Georgia,Serif;
        }
        
        #maincontainer
        {
            border: 1px dashed;
            padding: 5%;
            margin: 5%;
            width: 80%;
            height: 70%;
        }
    </style>
</head>
<body>
    <div id="maincontainer" runat="server">
        <p>
            <strong>
                <%=senderUsername%>, send a gift to
                <%=destinationUsername%>!</strong></p>
        <p>
            Use the Gifts API to plug into your existing gifts system!</p>
        <img src="gift.png" width="80" height="80" />
        <p>
            <input type="submit" value="..,,::::>>>>SEND<<<<::::,,.." /></p>
    </div>
</body>
</html>
