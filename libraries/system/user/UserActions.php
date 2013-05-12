<?php
/**
 * MseBase - PHP system to develop web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

/**
 * Handles the user actions for frontend and backend
 * 
 * TODO: Install script creates DB jobs to remove expired entries
 */

class UserActions {
	public static function login(){
		global $Config;
		
		// Check if there is a user already logged in 
		if(UserFunctions::getLoggedIn() == NULL){
			// No user logged in
			$result = (Url::getParts('result') != NULL ? Url::getParts('result') : 0);
			
			if($result == 0){
				?>
				<script type="text/javascript">
				function checkForm(){
					var form = document.loginForm;
					
					if(form.inputUsername.value == ""){
						alert("You need to enter a username");
						return false;
					}
					
					if(form.inputPassword.value == ""){
						alert("You need to enter a password");
						return false;
					}
					
					return true;
				}
				</script>
				<form name="loginForm" method="post" action="<?php echo (Define::get('baseSystem') == 1 ? Url::getAdminHttpBase() : Url::getHttpBase()); ?>/index.php?option=user&act=login&result=1" class="form-horizontal">
					<fieldset>
						<legend>Login</legend>
						<div class="control-group">
							<label class="control-label" for="inputUsername">Username</label>
							<div class="controls">
								<input type="text" name="inputUsername" id="inputUsername" placeholder="Username" />
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="inputPassword">Password</label>
							<div class="controls">
								<input type="password" name="inputPassword" id="inputPassword" placeholder="Password" />
							</div>
							<div class="controls">
								<a href="<?php echo (Define::get('baseSystem') == 1 ? Url::getAdminHttpBase() : Url::getHttpBase()) . "/index.php?option=user&act=resetPass"; ?>" style="padding-left:10px;">Forgot Password?</a>
							</div>
						</div>
						<div class="control-group">
							<div class="controls">
								<label class="checkbox">
									<input type="checkbox" name="rememberMe" checked /> Remember me
								</label>
								<button type="submit" class="btn" onClick="return checkForm();">Sign in</button>
							</div>
						</div>
					</fieldset>
				</form>
				<?php
			}
			
			if($result == 1){
				$info = Form::getParts();
				$error = false;
				$errorMessage = "";
				
				if(!isset($info['inputUsername']) || $info['inputUsername'] === ""){
					$error = true;
					$errorMessage = "Please enter a Username";
				}
				
				if(!isset($info['inputPassword']) || $info['inputPassword'] === ""){
					$error = true;
					$errorMessage = "Please enter a Password";
				}
				
				if($error == false){
					ImportClass("User.User");
					
					// Check if the username and password are good
					$user = new User();
					$user->setUsername($info['inputUsername']);
					
					if($user->userExists(true) == false){
						$error = true;
						$errorMessage = "Unknown Username";
					}
					
					if($user->passwordCorrect($info['inputPassword']) == false){
						$error = true;
						$errorMessage = "Invalid Password";
					}
					
					if($error == false){
						// At this point the username and password are good... login time
						// Create cookie and set a Session ID in it
						// The Session ID is a unique entry in the session db table
						ImportClass("User.UserSession");
						
						$userSession = new UserSession();
						
						if($userSession->register($user->getId(), (isset($info['rememberMe']) && $info['rememberMe'] === "on" ? true : false)) == true){
							// Save the session
							$userSession->save();
							
							// Create the cookie
							Cookie::add($Config->getSystemVar('cookieName'), $userSession->getSessionId());
							
							// Set the last login
							$user->setLastLogin(Date::getDbDateTimeFormat());
							$user->save();
							
							// Logged in
							Messages::setMessage("You have been logged in", Define::get("MessageLevelSuccess"));
							Url::redirect((Define::get('baseSystem') == 1 ? Url::getAdminHttpBase() : Url::getHttpBase()), 0, false);
						} else {
							// TODO: Shouldn't hit this
						}
					} else {
						Messages::setMessage($errorMessage, Define::get("MessageLevelError"));
						Url::redirect(UserFunctions::getLoginUrl(), 0, false);
					}
				} else {
					Messages::setMessage($errorMessage, Define::get("MessageLevelError"));
					Url::redirect(UserFunctions::getLoginUrl(), 0, false);
				}
			}
		} else {
			// User logged in - Should not have made it to this page
			Url::redirect(UserFunctions::getLogoutUrl(false));
		}
	}
	
	public static function logout(){
		global $Config;
		
		// Check if there is a user already logged in 
		if(UserFunctions::getLoggedIn() != NULL){
			ImportClass("User.UserSession");
			
			// Find the cookie
			$sessionId = Cookie::get($Config->getSystemVar('cookieName'));
			
			// Get the session
			$userSession = new UserSession($sessionId);
			
			// Destroy the session
			if($userSession->destroy() == true){
				// Destroy the cookie
				Cookie::delete($Config->getSystemVar('cookieName'));
				
				// Success
				Messages::setMessage("You have been logged out", Define::get("MessageLevelSuccess"));
				Url::redirect((Define::get('baseSystem') == 1 ? Url::getAdminHttpBase() : Url::getHttpBase()), 0, false);
			} else {
				// Session destroy failed -- could not logout
				// TODO: 
			}
		} else {
			// User not logged in - Should not have made it to this page
			Url::redirect(UserFunctions::getLoginUrl(false));
		}
	}
	
	public static function resetPassword(){
		global $Config;
		
		// Check if there is a key in the url
		if(Url::getParts('token') == NULL){
			// No token -- display reset page
			$result = (Url::getParts('result') != NULL ? Url::getParts('result') : 0);
			
			if($result == 0){
				?>
				<script type="text/javascript">
				function checkForm(){
					var form = document.resetPassForm;
					
					if(form.inputUsername.value == ""){
						alert("You need to enter a username");
						return false;
					}
					
					return true;
				}
				</script>
				<form name="resetPassForm" method="post" action="<?php echo (Define::get('baseSystem') == 1 ? Url::getAdminHttpBase() : Url::getHttpBase()); ?>/index.php?option=user&act=resetPass&result=1" class="form-horizontal">
					<fieldset>
						<legend>Reset Password</legend>
						<div class="control-group">
							<label class="control-label" for="inputUsername">Username</label>
							<div class="controls">
								<input type="text" name="inputUsername" id="inputUsername" placeholder="Username" />
							</div>
						</div>
						<div class="control-group">
							<div class="controls">
								<button type="submit" class="btn" onClick="return checkForm();">Reset</button>
							</div>
						</div>
					</fieldset>
				</form>
				<?php
			}
			
			if($result == 1){
				$info = Form::getParts();
				$error = false;
				$errorMessage = "";
				
				if(!isset($info['inputUsername']) || $info['inputUsername'] === ""){
					$error = true;
					$errorMessage = "Please enter a Username";
				}
				
				if($error == false){
					ImportClass("User.User");
					
					// Check if the username is good
					$user = new User();
					$user->setUsername($info['inputUsername']);
					
					if($user->userExists(true) == false){
						$error = true;
						$errorMessage = "Unknown Username";
					}
					
					if($error == false){
						// The user is valid
						// Generate a random key
						ImportClass("PhpasswordPusher.PHPasswordPusher");
						$uniqueId = PHPasswordPusher::createCredential($user->getId());
						
						// Send out an email
						if($uniqueId != NULL){
							ImportClass("Mailer.Mailer");
							$mailer = new Mailer();
							$mailer->setTo($user->getName(1), $user->getEmail(1));
							$mailer->setSubject("User Account Reset");
							$mailer->setMessage("A request to reset your password has been generated. Please go to " . (Define::get('baseSystem') == 1 ? Url::getAdminHttpBase() : Url::getHttpBase()) . "/index.php?option=user&act=resetPass&token=" . $uniqueId);
							
							if($mailer->send() == true){
								Messages::setMessage("Request sent", Define::get("MessageLevelSuccess"));
								Url::redirect(UserFunctions::getLoginUrl(), 0, false);
							} else {
								// TODO: Error
							}
						} else {
							// TODO: Error
						}
					} else {
						Messages::setMessage($errorMessage, Define::get("MessageLevelError"));
						Url::redirect("index.php?option=user&act=resetPass", 0, false);
					}
				} else {
					Messages::setMessage($errorMessage, Define::get("MessageLevelError"));
					Url::redirect("index.php?option=user&act=resetPass", 0, false);
				}
			}
		} else {
			// Token passed in -- process it
			ImportClass("PhpasswordPusher.PHPasswordPusher");
			
			// Find the token value
			$userId = 0;
			$token = Url::getParts('token');
			$result = PHPasswordPusher::retrieveCredential($token, $userId);
			
			// Check for retrieval error
			if($result == true){
				// Check for valid user id
				if($userId > 0){
					ImportClass("User.User");
					
					// Create a new user
					$user = new User($userId);
					
					// All good, finally display the password reset stuff
					$result = (Url::getParts('result') != NULL ? Url::getParts('result') : 0);
					
					if($result == 0){
						?>
						<script type="text/javascript">
						function checkForm(){
							if($("#inputPassword").val() == ""){
								$("#formMessages").html("You need to enter a password").removeClass("hidden");
								return false;
							} else if($("#inputConfirmPassword").val() == ""){
								$("#formMessages").html("You need to confirm the password").removeClass("hidden");
								return false;
							} else if($("#inputPassword").val() != $("#inputConfirmPassword").val()){
								$("#formMessages").html("The passwords don't match").removeClass("hidden");
								return false;
							}
							
							return true;
						}
						</script>
						<form name="resetForm" method="post" action="<?php echo (Define::get('baseSystem') == 1 ? Url::getAdminHttpBase() : Url::getHttpBase()); ?>/index.php?option=user&act=resetPass&token=<?php echo $token; ?>&result=1" class="form-horizontal">
							<fieldset>
								<legend>Reset Password</legend>
								<div id="formMessages" class="alert alert-error hidden"></div>
								<div class="control-group">
									<label class="control-label" for="inputPassword">Password</label>
									<div class="controls">
										<input type="password" name="inputPassword" id="inputPassword" value="" />
									</div>
								</div>
								<div class="control-group">
									<label class="control-label" for="inputConfirmPassword">Confirm Password</label>
									<div class="controls">
										<input type="password" name="inputConfirmPassword" id="inputConfirmPassword" value="" />
									</div>
								</div>
								<div class="control-group">
									<div class="controls">
										<button type="submit" class="btn" onClick="return checkForm();">Change Password</button>
									</div>
								</div>
							</fieldset>
						</form>
						<?php
					}
					
					if($result == 1){
						$info = Form::getParts();
						$error = false;
						$errorMessage = "";
						
						if(!isset($info['inputPassword']) || $info['inputPassword'] === ""){
							$error = true;
							$errorMessage = "Please enter a password";
						}
						
						if(!isset($info['inputConfirmPassword']) || $info['inputConfirmPassword'] === ""){
							$error = true;
							$errorMessage = "Please confirm the password";
						}
						
						if(isset($info['inputPassword']) && $info['inputPassword'] !== "" && $info['inputPassword'] !== $info['inputConfirmPassword']){
							$error = true;
							$errorMessage = "The passwords do not match";
						}
						
						if($error == false){
							$user->setPassword($info['inputPassword']);
							
							if($user->save() == true){
								PHPasswordPusher::removeCredentials($token);
								Messages::setMessage("Your password has been reset", Define::get("MessageLevelSuccess"));
								Url::redirect(UserFunctions::getLoginUrl(), 0, false);
							} else {
								// TODO: Error
							}
						} else {
							Messages::setMessage($errorMessage, Define::get("MessageLevelError"));
							Url::redirect(UserFunctions::getLoginUrl(), 0, false);
						}
					}
				}
			} else {
				// TODO: Handle Error
			}
		}
	}
	
}
?>