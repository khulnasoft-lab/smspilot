<div class="card">
    <div class="card-header">
        <h4 class="card-title"><i class="la la-code"></i> {lang_dashboard_tools_tabhookdoctitle}</h4>
    </div>

    <div class="card-body">
        <p>{lang_tools_webhookguide_line1}</p>
        <p>{lang_tools_webhookguide_line2}</p>
        <p>{lang_tools_webhookguide_line3}</p>
        <p>{lang_tools_webhookguide_line4}</p>
    	<pre class="bg-dark px-5 py-2">
    		<code>
try {
    $message = $_POST;

    /**
     * The message variable will be an array 
     * with the following sample content
     *
     * $message = [
     *   "secret" => "Webhook secret pass",
     *   "name" => "Contact name, false if unknown",
     *   "phone" => "Contact number, E164 format",
     *   "message" => "Message content here",
     *   "receive_date" => "Date received on device"
     * ];
     *
     */

    // Before acknowledging the message, you should first verify 
    // if it's really coming from our platform. 
    // Use the web hook secret password to verify messages

    $secret = "Your webhook secret password";

    if($message["secret"] == $secret):
    	// Verified, reply to phone number using the API send endpoint
    	// Read the API guide for more info
    else:
    	// Message not verified, ignore or log
    endif;
} catch (Exception $e) {
    // Something went wrong
}
    		</code>
    	</pre>
    	<p>{lang_tools_webhookguide_line5}</p>
    	<p>{lang_tools_webhookguide_line6}</p>
    </div>
</div>