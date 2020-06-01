<html>
   <head>
      <title>3-D Secure Example</title>
      <script type="text/javascript">
          function OnLoadEvent()
          {
            // Make the form post as soon as it has been loaded.
            document.ThreeDForm.submit();
          }
      </script>
   </head>
   <body onload="OnLoadEvent();">
      <p>
          If your browser does not start loading the page,
          press the button below.
          You will be sent back to this site after you
          authorize the transaction.
      </p>
      <form name="ThreeDForm" method="POST" action="{{$attr->getUrl()}}">
          <button type=submit>Click Here</button>
          <input type="hidden" name="PaReq" value="{{$attr->getData()}}" />
          <input type="hidden" name="TermUrl" value="{{$callback}}" />
          <input type="hidden" name="MD" value="{{$url_id}}" />
      </form>
   </body>
</html>