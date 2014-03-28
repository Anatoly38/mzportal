<html>
<head>
<script LANGUAGE = "JavaScript1.2">
var CAPICOM_STORE_OPEN_READ_ONLY = 0;
var CAPICOM_CURRENT_USER_STORE = 2;;
var CAPICOM_CERTIFICATE_FIND_TIME_VALID = 9;
var CAPICOM_ENCODE_BASE64 = 0;

function foo(str_s)
{
   try
   {     
      var SignedData = new ActiveXObject("CAPICOM.SignedData"); 
    
      var MyStore = new ActiveXObject("CAPICOM.Store");    
     MyStore.Open(CAPICOM_CURRENT_USER_STORE, "My", CAPICOM_STORE_OPEN_READ_ONLY);
     var Signer = new ActiveXObject("CAPICOM.Signer");    
     //var Signer.Certificate = MyStore.Certificates.Find(CAPICOM_CERTIFICATE_FIND_TIME_VALID).Item(1);
    
     
      SignedData.Content = str_s;
      var szSignature = SignedData.Sign(Signer, true, CAPICOM_ENCODE_BASE64); 
    
      document.getElementById('val').value = szSignature;    
   }
   catch (e)
   {
      alert("An error occurred when attempting to sign the content, the errot was: " + e.description);     
   }   
}
</script>
</head>
<body>
<INPUT  TYPE="button" VALUE="Sign object" onclick="foo('dfgdfgsdf')" />

<FORM NAME="input" ACTION="" METHOD="post" onsubmit="foo('dfgdfgsdf')">

<INPUT type="text" name="func" value="first"/>
<INPUT id="val" type="text" name="val" value=""/>

<INPUT  TYPE="SUBMIT" VALUE="Sign object" />
<INPUT  TYPE="BUTTON" NAME="Cancel" VALUE="Cancel" />
</form>
</body>
</html>