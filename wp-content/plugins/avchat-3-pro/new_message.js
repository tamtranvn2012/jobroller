	var isActive;
    var count = 0;
    var localTotal = 0;
    
    window.onfocus = function () { 
        console.log("window.onfocus")
        isActive = true; 
        count=0;    
        //we update the favicon
        updateFavIcon()
    }; 
    
    window.onblur = function () { 
        
        console.log("window.onblur")
        count=0;
        isActive = false; 
    };
    
    function newTotalUnreadMsgInInactiveTabs(total){
        console.log("newTotalUnreadMsgInInactiveTabs("+total+")")
        //total is the number of unread messages in inactive tabs inside AVChat

        //we also save the value locally in ase it needs to be used by onNewMessageReceivedInActiveTab
        localTotal=total;
        //we update the favicon
        updateFavIcon()
    }
    
    function updateFavIcon(){
        Tinycon.setBubble(parseInt(localTotal)+parseInt(count))
    }
    
    function  onNewMessageReceivedInActiveTab(){
        console.log("onNewMessageReceivedInActiveTab()")
        //this function is called when a new message is received in the active tab inside AVChat
        
        //we only update the favicon when the browser tab containing AVChat is not active
        if (!isActive){
            count++
            updateFavIcon()
        }
    }
