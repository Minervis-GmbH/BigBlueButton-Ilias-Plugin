<?php
// enable display errors only on dev systems
ini_set('display_errors', 1);

use BigBlueButton\Enum\Role;
use ILIAS\DI\Container;

$directory = strstr($_SERVER['SCRIPT_FILENAME'], 'Customizing', true);
if(empty($directory))
{
	$directory = getcwd();
}
chdir($directory);

require_once('./Services/Context/classes/class.ilContext.php');
require_once("./Services/Init/classes/class.ilInitialisation.php");
require_once('./Services/Language/classes/class.ilLanguage.php');


/**
 * JoinWithGuestLink initialization ilias class
 * @author  Jephte Abijuru <jephte.abijuru@minervis.com>
 * Credits to MultiVC
 * @version $Id$
 *
 */

class GuestLink
{
    const DEFAULT_LANG = 'de';

    const PLUGIN_DIR = 'Customizing/global/plugins/Services/Repository/RepositoryObject/BigBlueButton';

    /** @var GuestLink|null $instance */
    static private $instance;

    /** @var Container $dic */
    private $dic;

    private $bbb;

    /** @var int $refId */
    private int $refId = 0;

    /** @var string $client */
    private $client;

    /** @var ilObjBigBlueButton $object */
    private ilObjBigBlueButton $pluginObject;
    /** @var ilBigBlueButtonProtocol $object */
    private ilBigBlueButtonProtocol $pluginHelper;

    /** @var ilObject $settings */
    private ilObject $pluginConfig;

    /** @var string $meetingId */
    private $meetingId = 0;

    /** @var string $attendeePwd */
    private $attendeePwd;

    /** @var string $urlJoinMeeting */
    private string $urlJoinMeeting;

    /** @var string $userTitle */
    private string $userTitle = '';

    /** @var string $displayName */
    private string $displayName = '';

    /** @var string|null $guestPassword */
    private ?string $guestPassword = null;

    /** @var string[] $userAccept */
    private array $userAccept = [
        'termsOfUse' => false
    ];

    /** @var bool[] $errState */
    private array $errState = [
        'displayname'  => false,
        'termsOfUse' => false,
        'moderator' => false,
        'userLimit' => false
    ];

    /** @var string $userLang */
    private string $userLang = 'de';

    /** @var string[] $isoLangCode */
    private array $isoLangCode = [
        'de' => 'de-DE',
        'en' => 'en-US'
    ];


    /** @var array $formField */
    private $formField = [];

    /** @var ilTemplate $htmlTpl */
    private $htmlTpl;




    // BigBlueButton

    private function setMeetingId(): void
    {

        $this->meetingId = $this->pluginObject->getBBBId();//$rawMeetingId;
    }

    private function isMeetingRunning(): bool
    {
        try {
            $meetingParams = new \BigBlueButton\Parameters\IsMeetingRunningParameters($this->meetingId);
            $response = $this->bbb->isMeetingRunning($meetingParams);
            $running = $response->isRunning();
        } catch (Exception $e) {
            $running = false;
        }
        return $running;
    }

    private function getUrlJoinMeeting(): bool
    {

        if( !$this->isMeetingRunning() ) {
            return false;
        }
        $joinParams = new \BigBlueButton\Parameters\JoinMeetingParameters($this->meetingId, trim($this->userTitle . ' ') . $this->displayName, Role::VIEWER);
        $joinParams->setRedirect(true)
                    ->setRole('VIEWER')
                    ->setClientURL($this->dic->http()->request()->getUri())
        ;

        if( strlen($this->urlJoinMeeting = $this->bbb->getJoinMeetingURL($joinParams)) )
        {
            return true;
        }
        return false;
    }



    // Header-Redirect to BBB

    private function redirectToBBB(): void {
        header('Status: 303 See Other', false, 303);
        header('Location:' . $this->urlJoinMeeting);
        exit;
    }



    // Language Vars & HTML-Form

    private function setFormElements() {
        $input = function($name, $value, $type = 'text', $title = '', $class="", $addAttr = "") {
            return '<input type="' . $type . '" name="' . $name . '" value="' . $value . '" title="' . $title . '" placeholder="' . $title . '" class="' . $class . '"' . $addAttr . ' />';
        };
        $this->formField = [
            'user_title' => $input('user_title', $this->userTitle, 'text', 'Titel'),
            'display_name' => $input('display_name', $this->displayName, 'text', $this->txt('guest_displayname_input'), 'form-control'),
            'submit' => $input('submit', $this->txt('btntext_join_meeting'), 'submit', $this->txt('btntext_join_meeting'), 'btn btn-primary'),
            'guest_password' => $input('guest_password', $this->guestPassword, 'password', $this->txt('guest_password_input'), 'form-control', ' autocomplete="new-password"'),
            'guest_password_hidden' => $input('guest_password', $this->pluginObject->getAccessToken(), 'hidden', $this->txt('guest_password_input'), 'form-control'),
            'guest_login_button' => $input('guest_login_button', $this->txt('btntext_guest_login_button'), 'submit', $this->txt('btntext_guest_login'), 'btn btn-primary'),
        ];

    }

    private function setHtmlDocument()
    {
		$http_base = ILIAS_HTTP_PATH;
		if (strpos($http_base,'/m/')) {
			$http_base = strstr($http_base,'/m/',true).'/Customizing/global/plugins/Services/Repository/RepositoryObject/BigBlueButton';
		}

        $this->htmlTpl = new ilTemplate( dirname(__FILE__) . '/' . 'templates/tpl.guest.html', true, true);
        $this->htmlTpl->setVariable('USER_LANG', $this->isoLangCode[$this->userLang]);
        $this->htmlTpl->setVariable('HTTP_BASE', $http_base);
        $this->htmlTpl->setVariable('MEETING_TITLE', $this->getMeetingTitle());// . ' - ' . $this->getLangVar('big_blue_button'));
        $this->htmlTpl->setVariable('INFO_TOP_MODERATED_M', $this->txt('top_moderated_m'));
        $this->htmlTpl->setVariable('ERR_STATE_INPUT_FIELD', (int)$this->errState['displayname']);
        $this->htmlTpl->setVariable('ERR_MSG_INPUT_FIELD', !$this->errState['displayname'] ? '' : $this->txt('err_msg_displayname'));
        $this->htmlTpl->setVariable('ERR_STATE_USER_LIMIT', (int)$this->errState['userLimit'] );
        $this->htmlTpl->setVariable('ERR_MSG_USER_LIMIT', !$this->errState['userLimit'] ? '': $this->pluginConfig->getMaxConcurrentSessionsMsg());
        $this->htmlTpl->setVariable('ERR_STATE_TERMSOFUSE', (int)$this->errState['termsOfUse']);
        $this->htmlTpl->setVariable('VAL_TERMSOFUSE', (int)$this->userAccept['termsOfUse']);
        $this->htmlTpl->setVariable('TXT_ACCEPT_TERMSOFUSE', $this->txt('terms_of_use') );
        $this->htmlTpl->setVariable('ERR_STATE_MODERATOR', (int)$this->errState['moderator']);
        $this->htmlTpl->setVariable('ERR_MSG_MODERATOR', !$this->errState['moderator'] ? '' : $this->txt('wait_join_meeting'));
        $this->htmlTpl->setVariable('FORM_ACTION', filter_var('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL));
        $this->htmlTpl->setVariable('INFO_BOTTOM', $this->txt('join_info'));
        $this->htmlTpl->setVariable('INFO_REQUIREMENTS', $this->txt('tech_requirements_info'));


        if( $this->isUserLoggedIn() ) {
            #ilSession::set('guestLoggedIn', false);
            $this->htmlTpl->setVariable('INPUT_FIELD', $this->getFormField('display_name'));
            $this->htmlTpl->setVariable('SUBMIT_BUTTON', $this->getFormField('guest_password_hidden') . $this->getFormField('submit'));
        }
        // GUEST PASSWORD/LOGIN
        if( $this->isPwEnabled() && !$this->isUserLoggedIn() ) {
            if( isset($_POST['guest_password']) ) {
                $this->htmlTpl->setVariable('ERR_STATE_INPUT_FIELD', 1);
                $this->htmlTpl->setVariable('ERR_MSG_INPUT_FIELD', $this->txt('err_msg_guest_password'));
            }
            $this->htmlTpl->setVariable('INPUT_FIELD', $this->getFormField('guest_password'));
            $this->htmlTpl->setVariable('INPUT_FIELD_INFO', $this->txt('guest_password_input_info'));
            $this->htmlTpl->setVariable('SUBMIT_BUTTON', $this->getFormField('guest_login_button'));
        }

    }

    private function isPwEnabled() {
        return (bool)strlen(trim($this->pluginObject->getAccessToken()));
    }

    private function isUserLoggedIn() {
        return ilSession::get('guestLoggedIn');
    }

    private function checkPw( ?string $phrase = null ) {
        return trim($phrase) === trim($this->pluginObject->getAccessToken());
    }

    private function setGuestLoginState(): void
    {
        if ($this->dic->http()->wrapper()->post()->has('guest_password')) {
            $phrase = trim($this->dic->http()->wrapper()->post()->retrieve('guest_password', $this->dic->refinery()->kindlyTo()->string()));
            if ($this->isUserLoggedIn() || $this->checkPw($phrase)) {
                ilSession::set('guestLoggedIn', true);
            } else {
                ilSession::set('guestLoggedIn', false);
            }
        }else{
            ilSession::set('guestLoggedIn', true);
        }
    }


    private function getFormField($fieldName) {
        return strlen($field = $this->formField[$fieldName]) ? $field : '';
    }

    private function getMeetingTitle()
    {
        return $this->pluginObject->getTitle();
    }

    private function assignTranslatedLangVars()
    {
        $langFilePath = dirname(__FILE__) . '/lang/';

        // lang files
        $deLangFileName = $langFilePath . 'ilias_de.lang';
        $enLangFileName = $langFilePath . 'ilias_en.lang';

        // file content
        $deLangFileContent = file_get_contents($deLangFileName);
        $enLangFileContent = file_get_contents($enLangFileName);

        // content lines 2 array
        $deLangFileContentArr = explode("\n", $deLangFileContent);
        $enLangFileContentArr = explode("\n", $enLangFileContent);

        // build array with langVarKeys
        $enLangKeyVal = [];
        $i = 0;
        foreach( $deLangFileContentArr as $line ) {
            if( substr_count($line, '#:#') ) {
                list($key, $value) = explode('#:#', $line);
                $enLangKeyVal[] = trim($key) . '#:#' . trim($enLangFileContentArr[$i]);
                $i++;
            }
        }

        echo implode("\n", $enLangKeyVal);



    }

    private function txt(string $value): string
    {
        return ilLanguage::_lookupEntry( $this->userLang, 'rep_robj_xbbb','rep_robj_xbbb_' . $value);
    }

    private function setUserLangBySvrParam(): void
    {
        if( isset($this->dic->http()->request()->getServerParams()['HTTP_ACCEPT_LANGUAGE']) && strlen($this->dic->http()->request()->getServerParams()['HTTP_ACCEPT_LANGUAGE']) >= 2 ) {
            $lang = substr($this->dic->http()->request()->getServerParams()['HTTP_ACCEPT_LANGUAGE'], 0, 2);
            if (in_array($lang, ilLanguage::_getInstalledLanguages())) {
                $this->userLang = $lang;
            }else {
                $this->userLang = ilSession::get("lang");
            }
        }
    }

    // validation checks

    private function checkPostRequest()
    {
        $score = 0;
        if ($this->dic->http()->wrapper()->post()->has('display_name')){
            $this->displayName = trim($this->dic->http()->wrapper()->post()->retrieve('display_name', $this->dic->refinery()->kindlyTo()->string()));
            $score += 2;
            if($this->displayName == '') {
                $score -= 2;
                $this->errState['displayname'] = true;
            }
        }
        if ($this->dic->http()->wrapper()->post()->has('terms_of_use')) {
            $this->userAccept['termsOfUse'] = (bool) $this->dic->http()->wrapper()->post()->retrieve('terms_of_use', $this->dic->refinery()->kindlyTo()->int());
            $score += 4;
        } else {
            $this->errState['termsOfUse'] = true;
        }

        return $score >= 6;
    }

    private function validateInvitation()
    {
        switch( true ) {
            case $this->pluginConfig->isGuestGlabalAllowed() && $this->pluginObject->isGuestLinkAllowed():
                break;
            default:
                $this->httpExit(403);
        }
    }

    private function httpExit(int $code = 404)
    {
        $text = [
            403 => 'Forbidden',
            404 => 'Not Found'
        ];
        $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
        http_response_code($code);
        header($protocol . ' ' . $code . ' ' . $text[$code]);
        exit;
    }

    private function __construct()
    {

        ilContext::init(ilContext::CONTEXT_WEBDAV);
        ilInitialisation::initILIAS();

        global $DIC;
        $this->dic = $DIC;
        $this->client = $this->dic->http()->wrapper()->query()->retrieve('client_id', $this->dic->refinery()->kindlyTo()->string());
        $this->refId = $this->dic->http()->wrapper()->query()->retrieve('ref_id', $this->dic->refinery()->kindlyTo()->int());

        try {
            $this->pluginObject = $this->pluginConfig = ilObjectFactory::getInstanceByRefId($this->refId);
        } catch (ilDatabaseException $e) {
        } catch (ilObjectNotFoundException $e) {
            $this->httpExit(404);
        }
        
        $this->pluginHelper = new ilBigBlueButtonProtocol($this->pluginObject);

        // exit if not valid
        $this->validateInvitation();

        $this->setGuestLoginState();

        $this->setUserLangBySvrParam();
        // redirect to BBB if valid
        if( $this->checkPostRequest() ) {
            if($this->pluginObject->isMaxConcurrentSessionEnabled()){
                $available_sessions = $this->pluginHelper->getMaximumSessionsAvailable();
                if($available_sessions['max_sessions'] ||  (  key_exists($this->pluginObject->getBBBId(), $available_sessions['meetings']) && $available_sessions['meetings'][$this->pluginObject->getBBBId()]['userlimit'])){
                    $this->errState['userLimit'] = true;
                }
            }
            if( !$this->errState['displayname'] ) {
                $this->bbb = new ilBBB($this->pluginConfig->getSvrSalt(), $this->pluginConfig->getSvrPublicUrl());
                $this->attendeePwd = $this->pluginObject->getAttendeePwd();
                $this->setMeetingId();
                if( $this->getUrlJoinMeeting() ) {
                    $this->redirectToBBB();
                }
                $this->errState['moderator'] = true;
            }
        }


        $this->setFormElements();
        $this->setHtmlDocument();

    }

    public static function init()
    {
        if( self::$instance instanceof GuestLink) {
            return self::$instance;
        }
        return self::$instance = new self();
    }

    public function __toString(): string
    {
        return $this->htmlTpl->get();
    }

}

echo GuestLink::init();






