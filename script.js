//Javascript functions to facilitate PKG script editing
function insertAtCursor(myField, myValue) {
  //IE support
  if (document.selection) {
    myField.focus();
    sel = document.selection.createRange();
    sel.text = myValue;
  }
  //MOZILLA/NETSCAPE support
  else if (myField.selectionStart || myField.selectionStart == "0") {
    var startPos = myField.selectionStart;
    var endPos = myField.selectionEnd;
    myField.value = myField.value.substring(0, startPos)+myValue+myField.value.substring(endPos, myField.value.length);
  } else {
    myField.value += myValue;
  }
}
function addchdr(){
  insertAtCursor(document.details.pkgscript, "\r\nchdr /XXX@");
}
function addmkdr(){
  insertAtCursor(document.details.pkgscript, "\r\nmkdr /YYY@");
}
function adddown(){
  insertAtCursor(document.details.pkgscript, "\r\ndown http://ZZZ@");
}
function addecho(){
  insertAtCursor(document.details.pkgscript, "\r\necho hello world@");
}
function addcls(){
  insertAtCursor(document.details.pkgscript, "\r\ncls @");
}
function addwait(){
  insertAtCursor(document.details.pkgscript, "\r\nwait @");
}
function flagPkg(){
  document.details.what.value ="genPkg";
}
function previewPkg(){
  var myUrl = document.details.download_link.value;
  window.open ("pkggenerator.php?displayClose=true&testPkg="+myUrl, 'PKG_preview', config='height=400,width=600, toolbar=no, menubar=no, scrollbars=yes, resizable=yes,location=no, directories=no, status=no');
}
function pkgScriptHelp(){
  var myAlert = "Examples of acceptable Download Links\n\nAll files and sub-directories added to PKG script:\n\n> http://www.hbserver.com/downloads/dsdoom/\n> /downloads/dsdoom/\n\nSingle file added to PKG script:\n\n> http://www.hbserver.com/downloads/dsdoom/dsdoom.nds\n> http://www.externalServer.com/downloads/dsdoom/dsdoom.nds";
  alert(myAlert);
}
