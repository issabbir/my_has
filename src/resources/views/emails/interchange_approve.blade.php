<body style="background:#fff; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; margin:0; padding:0;color:#000;">
<div style="background:#fff; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; margin:0; padding:0;color:#000;">
    <table cellspacing="0" cellpadding="0" border="0" height="100%" width="100%">
        <tr>
            <td align="center" valign="top" style="padding:20px 0 20px 0;color:#000;">
                <!-- [ header starts here] -->
                <table bgcolor="#fff" cellspacing="0" cellpadding="10" border="0" width="650" style="border:7px solid #FFCC00;text-align: left;background: #fff;">
                    <tr>
                        <td valign="top" style="text-align:center;">
                            <a href="{{ url('/') }}" style="color:#1E7EC8;text-decoration:none;font-size:24px;">CHITTAGONG PORT AUTHORITY</a>
                        </td>
                    </tr>
                    <!-- [ middle starts here] -->
                    <tr>
                        <td valign="top">
                            <h1 style="font-size:22px; font-weight:normal; line-height:22px; margin:0 0 11px 0;color:#000;">Dear {{$name}},</h1>
                            <p style="font-size:12px; line-height:16px; margin:0 0 16px 0;color:#000;">Congratulations! Your are now approved to interchange your house. </p>
                            <p style="font-size:12px; line-height:16px; margin:0 0 16px 0;color:#000;"><a target="_blank" href="{{ $path }}">Please download approval letter by clicking this line.</a></p>
                        </td>
                    </tr>
                    <tr>
                        <td bgcolor="#fff" align="center" style="background:#fff;"><p style="font-size:12px; margin:0; text-align: left;">Thanks, <br />CPA</p></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
</body>
