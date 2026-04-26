<?php
// "Debug" page left exposed — leaks tmp file paths on upload
//
// ob_end_flush() + flush() forces output to be sent to the client
// incrementally. The padding loop at the end simulates a "slow" page
// that keeps the PHP process (and its temp files) alive long enough
// for the race condition to be exploitable.

phpinfo();
ob_end_flush();
flush();

// Keep the process alive for a bit — during this window the uploaded
// temp file still exists and can be included via LFI.
for ($i = 0; $i < 100; $i++) {
    echo str_repeat(" ", 4096);
    flush();
    usleep(10000); // 10ms × 100 = ~1 second total
}
