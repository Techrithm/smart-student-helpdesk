&lt;?php
// backend/debug/debug_session.php
// Debug script to check session status

require_once '../config/session.php';

header('Content-Type: text/html; charset=utf-8');
?&gt;
&lt;!DOCTYPE html&gt;
&lt;html&gt;
&lt;head&gt;
    &lt;title&gt;Session Debug&lt;/title&gt;
    &lt;style&gt;
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .info { background: white; padding: 20px; margin: 10px 0; border-radius: 5px; border-left: 4px solid #007bff; }
        .success { border-left-color: #28a745; }
        .error { border-left-color: #dc3545; }
        .warning { border-left-color: #ffc107; }
        h2 { margin-top: 0; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 3px; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        table td { padding: 8px; border: 1px solid #ddd; }
        table td:first-child { font-weight: bold; width: 200px; background: #f8f9fa; }
    &lt;/style&gt;
&lt;/head&gt;
&lt;body&gt;
    &lt;h1&gt;Session Debug Information&lt;/h1&gt;
    
    &lt;div class="info"&gt;
        &lt;h2&gt;Session Status&lt;/h2&gt;
        &lt;table&gt;
            &lt;tr&gt;
                &lt;td&gt;Session Started&lt;/td&gt;
                &lt;td&gt;&lt;?= session_status() === PHP_SESSION_ACTIVE ? '✓ Yes' : '✗ No' ?&gt;&lt;/td&gt;
            &lt;/tr&gt;
            &lt;tr&gt;
                &lt;td&gt;Session ID&lt;/td&gt;
                &lt;td&gt;&lt;?= session_id() ?: 'None' ?&gt;&lt;/td&gt;
            &lt;/tr&gt;
            &lt;tr&gt;
                &lt;td&gt;Session Name&lt;/td&gt;
                &lt;td&gt;&lt;?= session_name() ?&gt;&lt;/td&gt;
            &lt;/tr&gt;
            &lt;tr&gt;
                &lt;td&gt;Cookie Path&lt;/td&gt;
                &lt;td&gt;&lt;?= ini_get('session.cookie_path') ?&gt;&lt;/td&gt;
            &lt;/tr&gt;
            &lt;tr&gt;
                &lt;td&gt;Cookie Lifetime&lt;/td&gt;
                &lt;td&gt;&lt;?= ini_get('session.cookie_lifetime') ?&gt; seconds&lt;/td&gt;
            &lt;/tr&gt;
        &lt;/table&gt;
    &lt;/div&gt;

    &lt;div class="info &lt;?= isset($_SESSION['user_id']) ? 'success' : 'error' ?&gt;"&gt;
        &lt;h2&gt;Login Status&lt;/h2&gt;
        &lt;?php if (isset($_SESSION['user_id'])): ?&gt;
            &lt;p&gt;✓ &lt;strong&gt;User is LOGGED IN&lt;/strong&gt;&lt;/p&gt;
            &lt;table&gt;
                &lt;tr&gt;
                    &lt;td&gt;User ID&lt;/td&gt;
                    &lt;td&gt;&lt;?= $_SESSION['user_id'] ?&gt;&lt;/td&gt;
                &lt;/tr&gt;
                &lt;tr&gt;
                    &lt;td&gt;User Name&lt;/td&gt;
                    &lt;td&gt;&lt;?= $_SESSION['name'] ?? 'N/A' ?&gt;&lt;/td&gt;
                &lt;/tr&gt;
                &lt;tr&gt;
                    &lt;td&gt;User Role&lt;/td&gt;
                    &lt;td&gt;&lt;?= $_SESSION['role'] ?? 'N/A' ?&gt;&lt;/td&gt;
                &lt;/tr&gt;
            &lt;/table&gt;
        &lt;?php else: ?&gt;
            &lt;p&gt;✗ &lt;strong&gt;User is NOT logged in&lt;/strong&gt;&lt;/p&gt;
            &lt;p&gt;No session variables found. Please log in first.&lt;/p&gt;
        &lt;?php endif; ?&gt;
    &lt;/div&gt;

    &lt;div class="info"&gt;
        &lt;h2&gt;All Session Variables&lt;/h2&gt;
        &lt;pre&gt;&lt;?php print_r($_SESSION); ?&gt;&lt;/pre&gt;
    &lt;/div&gt;

    &lt;div class="info"&gt;
        &lt;h2&gt;All Cookies&lt;/h2&gt;
        &lt;pre&gt;&lt;?php print_r($_COOKIE); ?&gt;&lt;/pre&gt;
    &lt;/div&gt;

    &lt;div class="info"&gt;
        &lt;h2&gt;PHP Session Configuration&lt;/h2&gt;
        &lt;table&gt;
            &lt;tr&gt;
                &lt;td&gt;session.gc_maxlifetime&lt;/td&gt;
                &lt;td&gt;&lt;?= ini_get('session.gc_maxlifetime') ?&gt; seconds&lt;/td&gt;
            &lt;/tr&gt;
            &lt;tr&gt;
                &lt;td&gt;session.cookie_lifetime&lt;/td&gt;
                &lt;td&gt;&lt;?= ini_get('session.cookie_lifetime') ?&gt; seconds&lt;/td&gt;
            &lt;/tr&gt;
            &lt;tr&gt;
                &lt;td&gt;session.cookie_path&lt;/td&gt;
                &lt;td&gt;&lt;?= ini_get('session.cookie_path') ?&gt;&lt;/td&gt;
            &lt;/tr&gt;
            &lt;tr&gt;
                &lt;td&gt;session.cookie_domain&lt;/td&gt;
                &lt;td&gt;&lt;?= ini_get('session.cookie_domain') ?: '(empty)' ?&gt;&lt;/td&gt;
            &lt;/tr&gt;
            &lt;tr&gt;
                &lt;td&gt;session.cookie_httponly&lt;/td&gt;
                &lt;td&gt;&lt;?= ini_get('session.cookie_httponly') ? 'Yes' : 'No' ?&gt;&lt;/td&gt;
            &lt;/tr&gt;
            &lt;tr&gt;
                &lt;td&gt;session.use_strict_mode&lt;/td&gt;
                &lt;td&gt;&lt;?= ini_get('session.use_strict_mode') ? 'Yes' : 'No' ?&gt;&lt;/td&gt;
            &lt;/tr&gt;
        &lt;/table&gt;
    &lt;/div&gt;

    &lt;div class="info warning"&gt;
        &lt;h2&gt;Test Session Validation&lt;/h2&gt;
        &lt;p&gt;Click the button below to test the session check API (same one used by session-guard.js):&lt;/p&gt;
        &lt;button onclick="testSessionAPI()" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;"&gt;Test Session API&lt;/button&gt;
        &lt;div id="apiResult" style="margin-top: 10px;"&gt;&lt;/div&gt;
    &lt;/div&gt;

    &lt;script&gt;
        async function testSessionAPI() {
            const resultDiv = document.getElementById('apiResult');
            resultDiv.innerHTML = '&lt;p&gt;Testing...&lt;/p&gt;';
            
            try {
                const response = await fetch('../auth/check_session.php', {
                    method: 'GET',
                    cache: 'no-cache',
                    credentials: 'same-origin'
                });
                
                const result = await response.json();
                
                resultDiv.innerHTML = `
                    &lt;div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin-top: 10px;"&gt;
                        &lt;h3&gt;API Response:&lt;/h3&gt;
                        &lt;pre&gt;${JSON.stringify(result, null, 2)}&lt;/pre&gt;
                        &lt;p&gt;&lt;strong&gt;Status:&lt;/strong&gt; ${result.logged_in ? '✓ Logged In' : '✗ Not Logged In'}&lt;/p&gt;
                    &lt;/div&gt;
                `;
            } catch (error) {
                resultDiv.innerHTML = `
                    &lt;div style="background: #ffe6e6; padding: 15px; border-radius: 5px; margin-top: 10px; border-left: 4px solid #dc3545;"&gt;
                        &lt;h3&gt;Error:&lt;/h3&gt;
                        &lt;p&gt;${error.message}&lt;/p&gt;
                    &lt;/div&gt;
                `;
            }
        }
    &lt;/script&gt;

    &lt;div style="margin-top: 20px; padding: 15px; background: #e9ecef; border-radius: 5px;"&gt;
        &lt;p&gt;&lt;strong&gt;Quick Actions:&lt;/strong&gt;&lt;/p&gt;
        &lt;a href="../../frontend/student/login.html" style="display: inline-block; padding: 10px 15px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; margin-right: 10px;"&gt;Go to Login&lt;/a&gt;
        &lt;a href="../../frontend/student/dashboard.html" style="display: inline-block; padding: 10px 15px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin-right: 10px;"&gt;Go to Dashboard&lt;/a&gt;
        &lt;a href="javascript:location.reload()" style="display: inline-block; padding: 10px 15px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px;"&gt;Refresh&lt;/a&gt;
    &lt;/div&gt;
&lt;/body&gt;
&lt;/html&gt;
