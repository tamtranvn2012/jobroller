<%
''###################### AVChat 3.0 Configuration file ######################
''######################         .asp Version          ######################
''########## See avc_settings.php for explanation of each variable ##########
''#### 2 variables are sent via GET to this script:
''####   admin=true (when this script is executed by admin.swf)
''####   userId=XXX (This variable is sent to index.swf and admin.swf via GET and forwarded to this script. To edit it's value look in index.html and admin.html respectively.)

Dim connectionstring
connectionstring=""

Dim emoticonsurl
emoticonsurl="emoticons/standardPack/emoticons.xml"

Dim languagefile
languagefile="translations/en.xml"

Dim watermarkForOtherPeoplesStreams
watermarkForOtherPeoplesStreams="fullStar.png"

Dim kickUserAfterThisManySeconds
kickUserAfterThisManySeconds=0

Dim kickUserAfterThisManySecondsURL
kickUserAfterThisManySecondsURL=""

Dim allowEmailsAndUrlsInUsernames
allowEmailsAndUrlsInUsernames=1

Dim inviteLink
inviteLink=""

Dim disconnectButtonEnabled
disconnectButtonEnabled=1

Dim disconnectButtonLink
disconnectButtonLink="/"

Dim floodControlEnabled
floodControlEnabled=1

Dim floodControlDelay
floodControlDelay=300

Dim maxStreams
maxStreams=4

Dim allowVideoStreaming
allowVideoStreaming=1

Dim allowAudioStreaming
allowAudioStreaming=1

Dim allowPrivateStreaming
allowPrivateStreaming=1

Dim emoteIconsEnabled
emoteIconsEnabled=1

Dim youTubeVideosEnabled
youTubeVideosEnabled=1

Dim formattingEnabled
formattingEnabled=1

Dim sendFileToRoomsEnabled
sendFileToRoomsEnabled=1

Dim usersListType
usersListType="small"

Dim allowEmails
allowEmails=1

Dim allowUrls
allowUrls=1

Dim displayRoomOwners
displayRoomOwners=1

Dim showLoginError
showLoginError=0

Dim loginPageURL
loginPageURL="/"

Dim registerPageURL
registerPageURL="/"

Dim regiserandloginPageFrame
regiserandloginPageFrame="_self"

Dim maxUploadFileSize
maxUploadFileSize=524288

Dim textChatCharLimit
textChatCharLimit=200

Dim buzzButtonEnabled
buzzButtonEnabled=1

Dim secondsBetweenBuzzez
secondsBetweenBuzzez=5

Dim sendFileToUserEnabled
sendFileToUserEnabled=1

Dim pmEnabled
pmEnabled=1

Dim toggleRandomColors
toggleRandomColors=1

Dim disableGenderSelection
disableGenderSelection=0

Dim joinRoomsEnabled
joinRoomsEnabled=1

Dim maxRoomsOneCanBeIn
maxRoomsOneCanBeIn=4

Dim createRoomsEnabled
createRoomsEnabled=1

Dim freeVideoTime
freeVideoTime=3200

''!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! [+] DEPRECATED (moved to style.css) [+]!!!!!!!!!!!!!!!!!!!!
Dim backgroundImageAlpha
backgroundImageAlpha=20

DIm backgroundImageUrl
backgroundImageUrl="pattern_061.gif"

Dim backgroundImageScale
backgroundImageScale="tile"

Dim windowsCastShadows
windowsCastShadows = 1
''!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! [-] DEPRECATED (moved to style.css) [-]!!!!!!!!!!!!!!!!!!!!

Dim profileUrl
profileUrl=""

'' profileKey is DEPRECATED see avc_settings.php for explanations
Dim profileKey
profileKey="username"

Dim siteId
siteId=""

Dim coupleGenderEnabled
coupleGenderEnabled=1

Dim inviteEnabled
inviteEnabled=1

Dim roomsListEnabled
roomsListEnabled=1

Dim dropInRoom
dropInRoom=""

Dim miccamsettingsurl
miccamsettingsurl="audio_video_quality_profiles/512k_high_motion_medium_picture_quality_high_sound_quality.xml"

Dim automaticallyReduceBandwidthUsage
automaticallyReduceBandwidthUsage=1

Dim historyLength
historyLength=20

Dim autoStartCameras
autoStartCameras=0

Dim autoStartMyCamera
autoStartMyCamera=0

Dim autoStartMyCamAndMicOnlyIfIHaveACam
autoStartMyCamAndMicOnlyIfIHaveACam=1

Dim usnmaxchars
usnmaxchars=64

Dim usnminchars
usnminchars=3

Dim changegender
changegender=1

Dim changeuser
changeuser=1

DIm username
username=""

Dim gender
gender=""

Dim thumbnailUrl
thumbnailUrl=""

Dim applyLanguageFilter
applyLanguageFilter=1

Dim adminCanDeleteRooms
adminCanDeleteRooms=1

Dim adminCanBan
adminCanBan=1

Dim adminCanRemoveBan
adminCanRemoveBan=1

Dim adminCanKick
adminCanKick=1

Dim hiddenGenderEnabled
hiddenGenderEnabled=1

Dim showWhoIsTyping
showWhoIsTyping=1

Dim protectAgainstSpammyMessages
protectAgainstSpammyMessages=1

Dim adminCanViewPrivateMessages
adminCanViewPrivateMessages=1

Dim adminCanViewPrivateStreamsWithoutPermission
adminCanViewPrivateStreamsWithoutPermission=1

Dim jlmessages
jlmessages=1

Dim kbmessages
kbmessages=1

Dim camsArePrivateByDefault
camsArePrivateByDefault=0

Dim showImagesInline
showImagesInline=1

Dim adminCanAccessSettings
adminCanAccessSettings=1

Dim bandwidthChartEnabled
bandwidthChartEnabled=1

Dim showTimeStampsInTextChat
showTimeStampsInTextChat =0

Dim kickURL
kickURL=""

Dim banURL
banURL=""

Dim adminCanJoinPrivateRoomsWithoutPermission
adminCanJoinPrivateRoomsWithoutPermission=1

Dim adminCanViewIps
adminCanViewIps=1

Dim imagePreviewAreaWidthAndHeight
imagePreviewAreaWidthAndHeight=120

Dim adminGenderEnabled
adminGenderEnabled=1

Dim adminCanAccessBannPanel
adminCanAccessBannPanel=1

Dim adminCanViewExtraInfo
adminCanViewExtraInfo=1

Dim adminCanCloseRooms
adminCanCloseRooms=1

Dim adminCanJoinClosedAndFullRooms
adminCanJoinClosedAndFullRooms=1

Dim showYTVideosPreview
showYTVideosPreview=1

Dim showToAdminsTheUserIpInTextChat
showToAdminsTheUserIpInTextChat=1

Dim showMemoryUsage
showMemoryUsage=0

Dim parseEmoteIcons
parseEmoteIcons=1

Dim defaultSort
defaultSort="alphanumeric"

Dim userBecomesIdleAfterXSeconds
userBecomesIdleAfterXSeconds = 60

Dim downForMaintenance
downForMaintenance = 0

Dim kickAfterIdleTime
kickAfterIdleTime = 0

Dim whosTypingPosition
whosTypingPosition = 0

Dim maleImageUrl
maleImageUrl = ""

Dim femaleImageUrl
femaleImageUrl = ""

Dim coupleImageUrl
coupleImageUrl = ""

Dim adminImageUrl
adminImageUrl=""

Dim rightToLeft
rightToLeft = 0

Dim hideStatusBar
hideStatusBar = 0

Dim usersCanSwitchBetweenPrivateAndPublic
usersCanSwitchBetweenPrivateAndPublic = 1

Dim allowedRooms
allowedRooms = 0

Dim adminCanEditRooms
adminCanEditRooms = 1

Dim ipLookupServiceUrl
ipLookupServiceUrl = "http://whatismyipaddress.com/ip/"

Dim autoAddIpToUsername
autoAddIpToUsername = 0

Dim showWhoisBanKickInText
showWhoisBanKickInText = 1

Dim historyLengthForAdmin
historyLengthForAdmin=100

Dim adminCanKickOtherAdmins
adminCanKickOtherAdmins=0

Dim adminCanBanOtherAdmins
adminCanBanOtherAdmins=0

Dim adminCanViewHiddenAdmins
adminCanViewHiddenAdmins=1

Dim showPreviewButton
showPreviewButton=1

Dim showJLButton
showJLButton=1

Dim profileCountryFlag
profileCountryFlag=""

Dim upgradeUrl
upgradeUrl= ""

Dim showUserSideMenuOnTextArea
showUserSideMenuOnTextArea=0

Dim  showLast5ImagesThumbs
showLast5ImagesThumbs=1

Dim giftsEnabled
giftsEnabled=1

Dim  giftsUrl
giftsUrl="javascript:NewWindow=window.open('sendgift.php?destinationSiteId=DEST_SITEID&destinationUsername=DEST_USERNAME&senderSiteId=SENDR_SITEID&senderUsername=SENDR_USERNAME','newWin','width=550,height=380,left=0,top=0,toolbar=No,location=No,scrollbars=No,status=No,resizable=Yes,fullscreen=No'); NewWindow.focus(); void(0);"

'' hideEmoteIconPanelDelay is DEPRECATED see avc_settings.php for explanations
Dim hideEmoteIconPanelDelay
hideEmoteIconPanelDelay=3000

Dim typingEnabled
typingEnabled=1

Dim stylecssurl
stylecssurl="style.css"

Dim badnicksxmlurl
badnicksxmlurl="badnicks.xml"

Dim stopViewerButtonEnabled
stopViewerButtonEnabled=1

Dim showIgnorePMsButton
showIgnorePMsButton=1

Dim maxUsersInRoomsLimits
maxUsersInRoomsLimits = "[-1,2,10,15,50,100]"

Dim adminCanKickFrom1Room
adminCanKickFrom1Room=1

Dim timeFormat
timeFormat ="g:i a"

Dim columnsInRoomsPanel
columnsInRoomsPanel ="name users private owner created"

Dim showVideoFpsInfo
showVideoFpsInfo=1

Dim lineSpacing
lineSpacing = 0

Dim adminCanSilenceFromRoom
adminCanSilenceFromRoom = 1

Dim adminCanSilenceOtherAdmins
adminCanSilenceOtherAdmins = 0

Dim silenceDuration
silenceDuration = 30

Dim blockBuzzButtonEnabled
blockBuzzButtonEnabled = 1

Dim showNumberOfCamsAndMics
showNumberOfCamsAndMics = 1

Dim useEchoCancelation
useEchoCancelation = 0

Dim dragEnabled
dragEnabled = 1

Dim rotatingMessageTime
rotatingMessageTime = 0

Dim rotatingMessageUrl
rotatingMessageUrl = "rotate_messages.php"

Dim showOnlineTime
showOnlineTime = 1

Dim checkLinkUrl
checkLinkUrl = ""

Dim interpretLinks
interpretLinks = 1

Dim adminCanStopStreams
adminCanStopStreams = 1

Dim pushToTalkEnabled
pushToTalkEnabled = 1

Dim sendGiftThroughJSApi
sendGiftThroughJSApi = 0

Dim viewProfileThroughJSApi
viewProfileThroughJSApi = 0

Dim blockingAUserAlsoBlocksAccessToCam
blockingAUserAlsoBlocksAccessToCam=1

Dim userCanSeeWhoIsWatchingHim
userCanSeeWhoIsWatchingHim=1

Dim pushToTalkDefault
pushToTalkDefault =0

Dim userCanSwicthBetweenP2TAndAlwaysOn
userCanSwicthBetweenP2TAndAlwaysOn=1

Dim userCanBlockOtherUsers
userCanBlockOtherUsers = 1

Dim clearTextChatButtonStatus
clearTextChatButtonStatus =1

Dim hideUsersList
hideUsersList=0

Dim hideLeftSide
hideLeftSide=0

Dim defaultStateTextChatSoundButton
defaultStateTextChatSoundButton=1

Dim defaultUserColor
defaultUserColor="0x000000"

Dim flipTabMenu
flipTabMenu=0

Dim userCanSeeNSFWContent
userCanSeeNSFWContent=1

Dim enableNSFWFeature
enableNSFWFeature=1

Dim userNamePrefix
userNamePrefix=""

Dim gendersUrl
gendersUrl="genders.xml"

Dim showAdminsOnTop
showAdminsOnTop=1

Dim selectedTabInLoginScreen
selectedTabInLoginScreen="guest"

Dim enableOtherAccountOptions
enableOtherAccountOptions=1

Dim badWordsXmlUrl
badWordsXmlUrl="badwords.xml"

Dim applyNickNameFilter
applyNickNameFilter=1

Dim showUserLevelError
showUserLevelError=0

Dim enableChatHistoryButton
enableChatHistoryButton=1

Dim enableBlockViewRequestsButton
enableBlockViewRequestsButton=1

Dim enableOtherAccountOptionsForGuests
enableOtherAccountOptionsForGuests=1

Dim enableRegisterTab
enableRegisterTab=1

Dim enableModeratorForRooms
enableModeratorForRooms=1

Dim clientUniqueIdentifier
clientUniqueIdentifier=Request.ServerVariables("REMOTE_ADDR")

Dim enableAudioVideoOnlyMode
enableAudioVideoOnlyMode=0

Dim sortRoomListOn
sortRoomListOn="name"

Dim enableNewLineForEveryMessage
enableNewLineForEveryMessage=0

Dim showMobileChatHistory
showMobileChatHistory=1

Dim setChatHistoryLength
setChatHistoryLength=250

Dim enableReportSending
enableReportSending=1

Dim adminCanSeeReports
adminCanSeeReports=1

Dim moderatorsCanPromote
moderatorsCanPromote=1


''##################### DO NOT EDIT BELOW ############################

Response.Write("a=b")''as3 won't allow the var/value air to start with & so we echo some dummy data first
Response.write("&connectionstring=" &  Server.URLEncode(connectionstring))
Response.write("&emoticonsurl=" &  Server.URLEncode(emoticonsurl))
Response.write("&languagefile=" &  Server.URLEncode(languagefile))
Response.write("&watermarkForOtherPeoplesStreams=" &  Server.URLEncode(watermarkForOtherPeoplesStreams))
Response.write("&kickUserAfterThisManySeconds=" &  kickUserAfterThisManySeconds)
Response.write("&kickUserAfterThisManySecondsURL=" &  kickUserAfterThisManySecondsURL)
Response.write("&allowEmailsAndUrlsInUsernames=" &  allowEmailsAndUrlsInUsernames)
Response.write("&inviteLink=" &  Server.URLEncode(inviteLink))
Response.write("&disconnectButtonEnabled=" &  disconnectButtonEnabled)
Response.write("&disconnectButtonLink=" &  disconnectButtonLink)
Response.write("&floodControlEnabled=" &  floodControlEnabled)
Response.write("&floodControlDelay=" &  floodControlDelay)
Response.write("&maxStreams=" &  maxStreams)
Response.write("&allowVideoStreaming=" &  allowVideoStreaming)
Response.write("&allowAudioStreaming=" &  allowAudioStreaming)
Response.write("&allowPrivateStreaming=" &  allowPrivateStreaming)
Response.write("&emoteIconsEnabled=" &  emoteIconsEnabled)
Response.write("&youTubeVideosEnabled=" &  youTubeVideosEnabled)
Response.write("&formattingEnabled=" &  formattingEnabled)
Response.write("&sendFileToRoomsEnabled=" &  sendFileToRoomsEnabled)
Response.write("&usersListType=" &  usersListType)
Response.write("&allowEmails=" &  allowEmails)
Response.write("&allowUrls=" &  allowUrls)
Response.write("&displayRoomOwners=" &  displayRoomOwners)
Response.write("&showLoginError=" &  showLoginError)
Response.write("&loginPageURL=" & Server.URLEncode(loginPageURL))
Response.write("&registerPageURL=" &  Server.URLEncode(registerPageURL))
Response.write("&regiserandloginPageFrame=" &  regiserandloginPageFrame)
Response.write("&maxUploadFileSize=" &  maxUploadFileSize)
Response.write("&textChatCharLimit=" &  textChatCharLimit)
Response.write("&buzzButtonEnabled=" &  buzzButtonEnabled)
Response.write("&secondsBetweenBuzzez=" &  secondsBetweenBuzzez)
Response.write("&sendFileToUserEnabled=" &  sendFileToUserEnabled)
Response.write("&pmEnabled=" &  pmEnabled)
Response.write("&toggleRandomColors=" &  toggleRandomColors)
Response.write("&disableGenderSelection=" &  disableGenderSelection)
Response.write("&joinRoomsEnabled=" &  joinRoomsEnabled)
Response.write("&maxRoomsOneCanBeIn=" &  maxRoomsOneCanBeIn)
Response.write("&createRoomsEnabled=" &  createRoomsEnabled)
Response.write("&freeVideoTime=" &  freeVideoTime)
Response.write("&backgroundImageAlpha=" &  backgroundImageAlpha)
Response.write("&backgroundImageUrl=" &  Server.URLEncode(backgroundImageUrl))
Response.write("&backgroundImageScale=" & backgroundImageScale)
Response.write("&profileUrl=" &  Server.URLEncode(profileUrl))
Response.write("&profileKey=" &  profileKey)
Response.write("&siteId=" &  siteId)
Response.write("&coupleGenderEnabled=" &  coupleGenderEnabled)
Response.write("&inviteEnabled=" &  inviteEnabled)
Response.write("&roomsListEnabled=" &  roomsListEnabled)
Response.write("&dropInRoom=" &  dropInRoom)
Response.write("&miccamsettingsurl=" &  Server.URLEncode(miccamsettingsurl))
Response.write("&automaticallyReduceBandwidthUsage=" &  automaticallyReduceBandwidthUsage)
Response.write("&historyLength=" &  historyLength)
Response.write("&autoStartCameras=" &  autoStartCameras)
Response.write("&autoStartMyCamera=" &  autoStartMyCamera)
Response.write("&autoStartMyCamAndMicOnlyIfIHaveACam=" &  autoStartMyCamAndMicOnlyIfIHaveACam)
Response.write("&usnmaxchars=" &  usnmaxchars)
Response.write("&usnminchars=" &  usnminchars)
Response.write("&changegender=" &  changegender)
Response.write("&changeuser=" &  changeuser)
Response.write("&username=" &  Server.URLEncode(username))
Response.write("&gender=" &  gender)
Response.write("&thumbnailUrl=" &  Server.URLEncode(thumbnailUrl))
Response.write("&applyLanguageFilter=" &  applyLanguageFilter)
Response.write("&adminCanDeleteRooms=" &  adminCanDeleteRooms)
Response.write("&adminCanBan=" &  adminCanBan)
Response.write("&adminCanRemoveBan=" &  adminCanRemoveBan)
Response.write("&adminCanKick=" &  adminCanKick)
Response.write("&hiddenGenderEnabled=" &  hiddenGenderEnabled)
Response.write("&showWhoIsTyping=" &  showWhoIsTyping)
Response.write("&protectAgainstSpammyMessages=" &  protectAgainstSpammyMessages)
Response.write("&adminCanViewPrivateMessages=" &  adminCanViewPrivateMessages)
Response.write("&adminCanViewPrivateStreamsWithoutPermission=" &  adminCanViewPrivateStreamsWithoutPermission)
Response.write("&jlmessages=" & jlmessages)
Response.write("&kbmessages=" & kbmessages)
Response.write("&camsArePrivateByDefault=" & camsArePrivateByDefault)
Response.write("&showImagesInline=" & showImagesInline)
Response.write("&adminCanAccessSettings=" & adminCanAccessSettings)
Response.write("&bandwidthChartEnabled=" & bandwidthChartEnabled)
Response.write("&showTimeStampsInTextChat=" & showTimeStampsInTextChat)
Response.write("&kickURL=" & Server.URLEncode(kickURL))
Response.write("&banURL=" & Server.URLEncode(banURL))
Response.write("&windowsCastShadows=" & windowsCastShadows)
Response.write("&adminCanJoinPrivateRoomsWithoutPermission=" & adminCanJoinPrivateRoomsWithoutPermission)
Response.write("&adminCanViewIps=" & adminCanViewIps)
Response.write("&imagePreviewAreaWidthAndHeight=" & imagePreviewAreaWidthAndHeight)
Response.write("&adminGenderEnabled=" & adminGenderEnabled)
Response.write("&adminCanAccessBannPanel=" & adminCanAccessBannPanel)
Response.write("&adminCanViewExtraInfo=" & adminCanViewExtraInfo)
Response.write("&adminCanCloseRooms=" & adminCanCloseRooms)
Response.write("&adminCanJoinClosedAndFullRooms=" & adminCanJoinClosedAndFullRooms)
Response.write("&showYTVideosPreview=" & showYTVideosPreview)
Response.write("&showToAdminsTheUserIpInTextChat=" & showToAdminsTheUserIpInTextChat)
Response.write("&showMemoryUsage=" & showMemoryUsage)
Response.write("&parseEmoteIcons=" & parseEmoteIcons)
Response.write("&defaultSort=" & defaultSort)
Response.write("&userBecomesIdleAfterXSeconds=" & userBecomesIdleAfterXSeconds)
Response.write("&downForMaintenance=" & downForMaintenance)
Response.write("&kickAfterIdleTime=" & kickAfterIdleTime)
Response.write("&whosTypingPosition=" & whosTypingPosition)
Response.write("&maleImageUrl="&maleImageUrl)
Response.write("&femaleImageUrl="&femaleImageUrl)
Response.write("&coupleImageUrl="&coupleImageUrl)
Response.write("&adminImageUrl="&adminImageUrl)
Response.write("&rightToLeft=" & rightToLeft)
Response.write("&hideStatusBar=" & hideStatusBar)
Response.write("&usersCanSwitchBetweenPrivateAndPublic=" & usersCanSwitchBetweenPrivateAndPublic)
Response.write("&allowedRooms=" & allowedRooms)
Response.write("&adminCanEditRooms=" & adminCanEditRooms)
Response.write("&ipLookupServiceUrl=" & ipLookupServiceUrl)
Response.write("&autoAddIpToUsername=" & autoAddIpToUsername)
Response.write("&showWhoisBanKickInText=" & showWhoisBanKickInText)
Response.write("&historyLengthForAdmin=" & historyLengthForAdmin)
Response.write("&adminCanKickOtherAdmins=" & adminCanKickOtherAdmins)
Response.write("&adminCanBanOtherAdmins=" & adminCanBanOtherAdmins)
Response.write("&adminCanViewHiddenAdmins=" & adminCanViewHiddenAdmins)
Response.write("&showPreviewButton=" & showPreviewButton)
Response.write("&showJLButton=" & showJLButton)
Response.write("&profileCountryFlag=" & profileCountryFlag)
Response.write("&upgradeUrl=" & upgradeUrl)
Response.write("&showUserSideMenuOnTextArea=" & showUserSideMenuOnTextArea)
Response.write("&showLast5ImagesThumbs=" & showLast5ImagesThumbs)
Response.write("&giftsEnabled=" & giftsEnabled)
Response.write("&giftsUrl=" & giftsUrl)
Response.write("&hideEmoteIconPanelDelay=" & hideEmoteIconPanelDelay)
Response.write("&typingEnabled=" & typingEnabled)
Response.write("&stylecssurl=" & stylecssurl)
Response.write("&badnicksxmlurl=" & badnicksxmlurl)
Response.write("&stopViewerButtonEnabled=" & stopViewerButtonEnabled)
Response.write("&showIgnorePMsButton=" & showIgnorePMsButton)
Response.write("&maxUsersInRoomsLimits=" & maxUsersInRoomsLimits)
Response.write("&adminCanKickFrom1Room=" & adminCanKickFrom1Room)
Response.write("&timeFormat=" & timeFormat)
Response.write("&columnsInRoomsPanel=" & columnsInRoomsPanel)
Response.write("&showVideoFpsInfo=" & showVideoFpsInfo)
Response.write("&lineSpacing=" & lineSpacing)
Response.write("&adminCanSilenceFromRoom=" & adminCanSilenceFromRoom)
Response.write("&adminCanSilenceOtherAdmins=" & adminCanSilenceOtherAdmins)
Response.write("&silenceDuration=" & silenceDuration)
Response.write("&blockBuzzButtonEnabled=" & blockBuzzButtonEnabled)
Response.write("&showNumberOfCamsAndMics=" & showNumberOfCamsAndMics)
Response.write("&useEchoCancelation=" & useEchoCancelation)
Response.write("&dragEnabled=" & dragEnabled)
Response.write("&rotatingMessageTime=" & rotatingMessageTime)
Response.write("&rotatingMessageUrl=" & rotatingMessageUrl)
Response.write("&showOnlineTime=" & showOnlineTime)
Response.write("&checkLinkUrl=" & checkLinkUrl)
Response.write("&interpretLinks=" & interpretLinks)
Response.write("&adminCanStopStreams=" & adminCanStopStreams)
Response.write("&pushToTalkEnabled=" & pushToTalkEnabled)
Response.write("&sendGiftThroughJSApi=" & sendGiftThroughJSApi)
Response.write("&viewProfileThroughJSApi=" & viewProfileThroughJSApi)
Response.write("&blockingAUserAlsoBlocksAccessToCam=" & blockingAUserAlsoBlocksAccessToCam)
Response.write("&userCanSeeWhoIsWatchingHim=" & userCanSeeWhoIsWatchingHim)
Response.write("&pushToTalkDefault=" & pushToTalkDefault)
Response.write("&userCanSwicthBetweenP2TAndAlwaysOn=" & userCanSwicthBetweenP2TAndAlwaysOn)
Response.write("&userCanBlockOtherUsers=" & userCanBlockOtherUsers)
Response.write("&clearTextChatButtonStatus=" & clearTextChatButtonStatus)
Response.write("&hideUsersList=" & hideUsersList)
Response.write("&hideLeftSide=" & hideLeftSide)
Response.write("&defaultStateTextChatSoundButton=" & defaultStateTextChatSoundButton)
Response.write("&defaultUserColor=" & defaultUserColor)
Response.write("&flipTabMenu=" & flipTabMenu)
Response.write("&userCanSeeNSFWContent=" & userCanSeeNSFWContent)
Response.write("&enableNSFWFeature=" & enableNSFWFeature)
Response.write("&userNamePrefix=" & userNamePrefix)
Response.write("&gendersUrl=" & gendersUrl)
Response.write("&showAdminsOnTop=" & showAdminsOnTop)
Response.write("&selectedTabInLoginScreen=" & selectedTabInLoginScreen)
Response.write("&enableOtherAccountOptions=" & enableOtherAccountOptions)
Response.write("&badWordsXmlUrl=" & badWordsXmlUrl)
Response.write("&applyNickNameFilter=" & applyNickNameFilter)
Response.write("&showUserLevelError=" & showUserLevelError)
Response.write("&enableChatHistoryButton=" & enableChatHistoryButton)
Response.write("&enableBlockViewRequestsButton=" & enableBlockViewRequestsButton)
Response.write("&enableOtherAccountOptionsForGuests=" & enableOtherAccountOptionsForGuests)
Response.write("&enableRegisterTab=" & enableRegisterTab)
Response.write("&enableModeratorForRooms=" & enableModeratorForRooms)
Response.write("&clientUniqueIdentifier=" & clientUniqueIdentifier)
Response.write("&enableAudioVideoOnlyMode=" & enableAudioVideoOnlyMode)
Response.write("&sortRoomListOn=" & sortRoomListOn)
Response.write("&enableNewLineForEveryMessage=" & enableNewLineForEveryMessage)
Response.write("&showMobileChatHistory=" & showMobileChatHistory)
Response.write("&showMobileChatHistory=" & setChatHistoryLength)
Response.write("&enableReportSending=" & enableReportSending)
Response.write("&adminCanSeeReports=" & adminCanSeeReports)
Response.write("&moderatorsCanPromote=" & moderatorsCanPromote)

%>