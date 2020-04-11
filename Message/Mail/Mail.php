<?php
/**
 * Orange Management
 *
 * PHP Version 7.4
 *
 * @package   phpOMS\Message\Mail
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace phpOMS\Message\Mail;

use phpOMS\System\CharsetType;
use phpOMS\System\MimeType;
use phpOMS\Utils\MbStringUtils;

/**
 * Mail class.
 *
 * @package phpOMS\Message\Mail
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
class Mail
{
    /**
     * Mail id.
     *
     * @var string
     * @since 1.0.0
     */
    protected string $id = '';

    /**
     * Mail from.
     *
     * @var string
     * @since 1.0.0
     */
    protected string $from = '';

    /**
     * Mail from.
     *
     * @var string
     * @since 1.0.0
     */
    protected string $fromName = '';

    /**
     * Mail to.
     *
     * @var array
     * @since 1.0.0
     */
    protected array $to = [];

    /**
     * Mail subject.
     *
     * @var string
     * @since 1.0.0
     */
    protected string $subject = '';

    /**
     * Mail cc.
     *
     * @var array
     * @since 1.0.0
     */
    protected array $cc = [];

    /**
     * Mail bcc.
     *
     * @var array
     * @since 1.0.0
     */
    protected array $bcc = [];

    /**
     * Mail attachments.
     *
     * @var array
     * @since 1.0.0
     */
    protected array $attachment = [];

    /**
     * Mail body.
     *
     * @var string
     * @since 1.0.0
     */
    protected string $body = '';

    /**
     * Mail overview.
     *
     * @var string
     * @since 1.0.0
     */
    protected string $overview = '';

    /**
     * Mail alt.
     *
     * @var string
     * @since 1.0.0
     */
    protected string $bodyAlt = '';

    /**
     * Mail mime.
     *
     * @var string
     * @since 1.0.0
     */
    protected string $bodyMime = '';

    /**
     * Mail body.
     *
     * @var string
     * @since 1.0.0
     */
    protected string $icalBody = '';

    /**
     * Mail header.
     *
     * @var string
     * @since 1.0.0
     */
    protected string $header = '';

    /**
     * Word wrap.
     *
     * @var int
     * @since 1.0.0
     */
    protected int $wordWrap = 72;

    /**
     * Encoding.
     *
     * @var string
     * @since 1.0.0
     */
    protected string $encoding = EncodingType::E_8BIT;

    /**
     * Mail content type.
     *
     * @var string
     * @since 1.0.0
     */
    protected string $contentType = MimeType::M_TXT;

    /**
     * Mail message type.
     *
     * @var string
     * @since 1.0.0
     */
    protected string $messageType = '';

    /**
     * Mail from.
     *
     * @var null|\DateTime
     * @since 1.0.0
     */
    protected ?\DateTime $messageDate = null;

    /**
     * Should confirm reading
     *
     * @var bool
     * @since 1.0.0
     */
    protected bool $confirmReading = false;

    private string $signKeyFile = '';
    private string $signCertFile = '';
    private string $signExtraFile = '';
    private string $signKeyPass = '';

    /**
     * Constructor.
     *
     * @param mixed $id Id
     *
     * @since 1.0.0
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Set body.
     *
     * @param string $body Mail body
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function setBody(string $body) : void
    {
        $this->body = $body;
    }

    /**
     * Set body alt.
     *
     * @param string $body Mail body
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function setBodyAlt(string $body) : void
    {
        $this->bodyAlt     = $body;
        $this->contentType = MimeType::M_ALT;
        $this->setMessageType();
    }

    /**
     * Set body.
     *
     * @param array $overview Mail overview
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function setOverview(array $overview) : void
    {
        $this->overview = $overview;
    }

    /**
     * Set encoding.
     *
     * @param int $encoding Mail encoding
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function setEncoding(int $encoding) : void
    {
        $this->encoding = $encoding;
    }

    /**
     * Set content type.
     *
     * @param int $contentType Mail content type
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function setContentType(int $contentType) : void
    {
        $this->contentType = empty($this->bodyAlt) ? $contentType : MimeType::M_ALT;
    }

    /**
     * Set subject
     *
     * @param string $subject Subject
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function setSubject(string $subject) : void
    {
        $this->subject = $subject;
    }

    /**
     * Set the from address
     *
     * @param string $mail Mail address
     * @param string $name Name
     *
     * @return bool
     *
     * @since 1.0.0
     */
    public function setFrom(string $mail, string $name = '') : bool
    {
        $mail = $this->normalizeEmailAddress($mail);
        $name = $this->normalizeName($name);

        if ($mail === null) {
            return false;
        }

        $this->from     = $mail;
        $this->fromName = $name;

        return true;
    }

    /**
     * Add a to address
     *
     * @param string $mail Mail address
     * @param string $name Name
     *
     * @return bool
     *
     * @since 1.0.0
     */
    public function addTo(string $mail, string $name = '') : bool
    {
        $mail = $this->normalizeEmailAddress($mail);
        $name = \trim($name);

        if ($mail === null) {
            return false;
        }

        $this->to[$mail] = $name;

        return true;
    }

    /**
     * Get to addresses
     *
     * @return array
     *
     * @since 1.0.0
     */
    public function getTo() : array
    {
        return $this->to;
    }

    /**
     * Add a cc address
     *
     * @param string $mail Mail address
     * @param string $name Name
     *
     * @return bool
     *
     * @since 1.0.0
     */
    public function addCc(string $mail, string $name = '') : bool
    {
        $mail = $this->normalizeEmailAddress($mail);
        $name = \trim($name);

        if ($mail === null) {
            return false;
        }

        $this->cc[$mail] = $name;

        return true;
    }

    /**
     * Add a bcc address
     *
     * @param string $mail Mail address
     * @param string $name Name
     *
     * @return bool
     *
     * @since 1.0.0
     */
    public function addBcc(string $mail, string $name = '') : bool
    {
        $mail = $this->normalizeEmailAddress($mail);
        $name = \trim($name);

        if ($mail === null) {
            return false;
        }

        $this->bcc[$mail] = $name;

        return true;
    }

    /**
     * Add an attachment
     *
     * @param string $path        Path to the file
     * @param string $name        Name of the file
     * @param string $encoding    Encoding
     * @param string $type        Mime type
     * @param string $disposition Disposition
     *
     * @return bool
     *
     * @since 1.0.0
     */
    public function addAttachment(
        string $path,
        string $name = '',
        string $encoding = EncodingType::E_BASE64,
        string $type = '',
        string $disposition = DispositionType::ATTACHMENT
    ) : bool {
        if ((bool) \preg_match('#^[a-z]+://#i', $path)) {
            return false;
        }

        $info = [];
        \preg_match('#^(.*?)[\\\\/]*(([^/\\\\]*?)(\.([^.\\\\/]+?)|))[\\\\/.]*$#m', $path, $info);
        $filename = $info[2] ?? '';

        $this->attachment[] = [
            'path'        => $path,
            'filename'    => $filename,
            'name'        => $name,
            'encoding'    => $encoding,
            'type'        => $type,
            'string'      => false,
            'disposition' => $disposition,
            'id'          => $name,
        ];

        $this->setMessageType();

        return true;
    }

    /**
     * The email should be confirmed by the receivers
     *
     * @param string $confirm Should be confirmed?
     *
     * @return void
     *
     * @sicne 1.0.0
     */
    public function shouldBeConfirmed(bool $confirm = true) : void
    {
        $this->confirmReading = $confirm;
    }

    /**
     * Normalize an email address
     *
     * @param string $mail Mail address
     *
     * @return null|string
     *
     * @since 1.0.0
     */
    private function normalizeEmailAddress(string $mail) : ?string
    {
        $mail = \trim($mail);
        $pos  = \strrpos($mail, '@');

        if ($pos === false || !\filter_var($mail, \FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        $normalized = \idn_to_ascii($mail);

        return $normalized === false ? $mail : $normalized;
    }

    /**
     * Normalize an email name
     *
     * @param string $name Name
     *
     * @return string
     *
     * @since1 1.0.0
     */
    private function normalizeName(string $name) : string
    {
        return \trim(\preg_replace("/[\r\n]+/", '', $name));
    }

    /**
     * Parsing an email containing a name
     *
     * @param string $mail Mail string
     *
     * @return array
     *
     * @since 1.0.0
     */
    private function parseEmailAddress(string $mail) : array
    {
        $addresses = [];
        $list      = \explode(',', $mail);

        foreach ($list as $address) {
            $address = \trim($address);

            if (\stripos($address, '<') === false) {
                if (($address = $this->normalizeEmailAddress($address)) !== null) {
                    $addresses[$address] = '';
                }
            } else {
                $parts   = \explode('<', $address);
                $address = \trim(\str_replace('>', '', $parts[1]));

                if (($address = $this->normalizeEmailAddress($address)) !== null) {
                    $addresses[$address] = \trim(\str_replace(['"', '\''], '', $parts[0]));
                }
            }
        }

        return $addresses;
    }

    /**
     * Check if text has none ascii characters
     *
     * @param string $text Text to check
     *
     * @return bool
     *
     * @since 1.0.0
     */
    private function hasNoneASCII(string $text) : bool
    {
        return (bool) \preg_match('/[\x80-\xFF]/', $text);
    }

    /**
     * Define the message type based on the content
     *
     * @return void
     *
     * @since 1.0.0
     */
    private function setMessageType() : void
    {
        $this->messageType = '';

        $type = [];
        if (!empty($this->bodyAlt)) {
            $type[] = DispositionType::ALT;
        }

        foreach ($this->attachment as $attachment) {
            if ($attachment['disposition'] === DispositionType::INLINE) {
                $type[] = DispositionType::INLINE;
            } elseif ($attachment['disposition'] === DispositionType::ATTACHMENT) {
                $type[] = DispositionType::ATTACHMENT;
            }
        }

        $this->messageType = \implode('_', $type);
        $this->messageType = empty($this->messageType) ? DispositionType::PLAIN : $this->messageType;
    }

    /**
     * Create the mail body
     *
     * @return string
     *
     * @since 1.0.0
     */
    public function createBody() : string
    {
        $this->id = empty($this->id) ? $this->generatedId() : $this->id;

        $output = '';
        $boundary    = [];
        $boundary[0] = 'b0_' . $this->id;
        $boundary[1] = 'b1_' . $this->id;
        $boundary[2] = 'b2_' . $this->id;
        $boundary[3] = 'b3_' . $this->id;

        $output .= !empty($this->signKeyFile) ? $this->generateMimeHeader($boundary) . $this->endOfLine : '';

        $body = $this->wrapText($this->body, $this->wordWrap, false);
        $bodyEncoding = $this->encoding;
        $bodyCharset  = $this->charset;

        if ($bodyEncoding === EncodingType::E_8BIT && !((bool) \preg_match('/[\x80-\xFF]/', $body))) {
            $bodyEncoding = EncodingType::E_7BIT;
            $bodyCharset  = CharsetType::ASCII;
        }

        if ($this->encoding !== EncodingType::E_BASE64 && ((bool) \preg_match('/^(.{' . (63 + \strlen($this->endOfLine)) . ',})/m', $body))) {
            $bodyEncoding = EncodingType::E_QUOTED;
        }

        $bodyAlt = $this->wrapText($this->bodyAlt, $this->wordWrap, false);
        $bodyAltEncoding = $this->encoding;
        $bodyAltCharset  = $this->charset;

        if ($bodyAlt !== '') {
            if ($bodyAltEncoding === EncodingType::E_8BIT && !((bool) \preg_match('/[\x80-\xFF]/', $bodyAlt))) {
                $bodyAltEncoding = EncodingType::E_7BIT;
                $bodyAltCharset  = CharsetType::ASCII;
            }

            if ($this->encoding !== EncodingType::E_BASE64 && ((bool) \preg_match('/^(.{' . (63 + \strlen($this->endOfLine)) . ',})/m', $bodyAlt))) {
                $bodyAltEncoding = EncodingType::E_QUOTED;
            }
        }

        $mimeBody = 'This is a multi-part message in MIME format.' . $this->endOfLine . $this->endOfLine;

        switch ($this->messageType) {
            case DispositionType::INLINE:
            case DispositionType::ATTACHMENT:
                $body .= $mimeBody;
                $body .= $this->getBoundary($boundary[0], $bodyCharset, $this->contentType, $bodyEncoding);
                $body .= $this->encodeString($this->body, $bodyEncoding);
                $body .= $this->endOfLine;
                $body .= $this->attachAll($this->messageType, $boundary[0]);
                break;
            case DispositionType::INLINE . '_' . DispositionType::ATTACHMENT:
                $body .= $mimeBody;
                $body .= '--' . $boundary[0] . $this->endOfLine;
                $body .= 'Content-Type: ' . MimeType::M_RELATED . ';' . $this->endOfLine;
                $body .= ' boundary ="' . $boundary[1] . '";' . $this->endOfLine;
                $body .= ' type ="' . MimeType::M_HTML . '";' . $this->endOfLine;
                $body .= $this->endOfLine;
                $body .= $this->getBoundary($boundary[1], $bodyCharset, $this->contentType, $bodyEncoding);
                $body .= $this->encodeString($this->body, $bodyEncoding);
                $body .= $this->endOfLine;
                $body .= $this->attachAll(DispositionType::INLINE, $boundary[1]);
                $body .= $this->endOfLine;
                $body .= $this->attachAll(DispositionType::ATTACHMENT, $boundary[1]);
                break;
            case DispositionType::ALT:
                $body .= $mimeBody;
                $body .= $this->getBoundary($boundary[0], $bodyAltCharset, MimeType::M_TEXT, $bodyAltEncoding);
                $body .= $this->encodeString($this->bodyAlt, $bodyAltEncoding);
                $body .= $this->endOfLine;
                $body .= $this->getBoundary($boundary[0], $bodyCharset, MimeType::M_HTML, $bodyEncoding);
                $body .= $this->encodeString($this->body, $bodyEncoding);
                $body .= $this->endOfLine;

                if (!empty($this->icalBody)) {
                    $method    = ICALMethodType::REQUEST;
                    $constants = ICALMethodType::getConstants();

                    foreach ($constants as $enum) {
                        if (\stripos($this->icalBody, 'METHOD:' . $enum) !== false
                            || \stripos($this->icalBody, 'METHOD: ' . $enum) !== false
                        ) {
                            $method = $enum;
                            break;
                        }
                    }

                    $body .= $this->getBoundary($boundary[0], $this->charset, MimeType::M_ICS . '; method=' . $method, $this->encoding);
                    $body .= $this->encodeString($this->icalBody, $this->encoding);
                    $body .= $this->endOfLine;
                }

                $body .= $this->endOfLine . '--' . $boundary[0] . '--' . $this->endOfLine;
            break;
            case DispositionType::ALT . '_' . DispositionType::INLINE:
                $body .= $mimeBody;
                $body .= $this->getBoundary($boundary[0], $bodyAltCharset, MimeType::M_TEXT, $bodyAltEncoding);
                $body .= $this->encodeString($this->bodyAlt, $bodyAltEncoding);
                $body .= $this->endOfLine;
                $body .= '--' . $boundary[0] . $this->endOfLine;
                $body .= 'Content-Type: ' . MimeType::M_RELATED . ';' . $this->endOfLine;
                $body .= ' boundary="' . $boundary[1] . '";' . $this->endOfLine;
                $body .= ' type="' . MimeType::M_HTML . '";' . $this->endOfLine;
                $body .= $this->endOfLine;
                $body .= $this->getBoundary($boundary[1], $bodyCharset, MimeType::M_HTML, $bodyEncoding);
                $body .= $this->encodeString($this->body, $bodyEncoding);
                $body .= $this->endOfLine;
                $body .= $this->attachAll(DispositionType::INLINE, $boundary[1]);
                $body .= $this->endOfLine;
                $body .= $this->endOfLine . '--' . $boundary[0] . '--' . $this->endOfLine;
                break;
            case DispositionType::ALT . '_' . DispositionType::ATTACHMENT:
                $body .= $mimeBody;
                $body .= '--' . $boundary[0] . $this->endOfLine;
                $body .= 'Content-Type: ' . MimeType::M_ALT . ';' . $this->endOfLine;
                $body .= ' boundary="' . $boundary[1] . '"' . $this->endOfLine;
                $body .= $this->endOfLine;
                $body .= $this->getBoundary($boundary[1], $bodyAltCharset, MimeType::M_TEXT, $bodyAltEncoding);
                $body .= $this->encodeString($this->bodyAlt, $bodyAltEncoding);
                $body .= $this->endOfLine;
                $body .= $this->getBoundary($boundary[1], $bodyCharset, MimeType::M_HTML, $bodyEncoding);
                $body .= $this->encodeString($this->body, $bodyEncoding);
                $body .= $this->endOfLine;

                if (!empty($this->icalBody)) {
                    $method    = ICALMethodType::REQUEST;
                    $constants = ICALMethodType::getConstants();

                    foreach ($constants as $enum) {
                        if (\stripos($this->icalBody, 'METHOD:' . $enum) !== false
                            || \stripos($this->icalBody, 'METHOD: ' . $enum) !== false
                        ) {
                            $method = $enum;
                            break;
                        }
                    }

                    $body .= $this->getBoundary($boundary[1], $this->charset, MimeType::M_ICS . '; method=' . $method, $this->encoding);
                    $body .= $this->encodeString($this->icalBody, $this->encoding);
                }

                $body .= $this->endOfLine . '--' . $boundary[1] . '--' . $this->endOfLine;
                $body .= $this->endOfLine;
                $body .= $this->attachAll(DispositionType::ATTACHMENT, $boundary[0]);
            break;
            case DispositionType::ALT . '_' . DispositionType::INLINE . '_' . DispositionType::ATTACHMENT:
                $body .= $mimeBody;
                $body .= '--' . $boundary[0] . $this->endOfLine;
                $body .= 'Content-Type: ' . MimeType::M_ALT . $this->endOfLine;
                $body .= ' boundary="' . $boundary[1] . '"' . $this->endOfLine;
                $body .= $this->endOfLine;
                $body .= $this->getBoundary($boundary[1], $bodyAltCharset, MimeType::M_TEXT, $bodyAltEncoding);
                $body .= $this->encodeString($this->bodyAlt, $bodyAltEncoding);
                $body .= $this->endOfLine;
                $body .= '--' . $boundary[1] . $this->endOfLine;
                $body .= 'Content-Type: ' . MimeType::M_RELATED . ';' . $this->endOfLine;
                $body .= ' boundary="' . $boundary[2] . '"' . $this->endOfLine;
                $body .= ' type="' . MimeType::M_HTML . '"' . $this->endOfLine;
                $body .= $this->endOfLine;
                $body .= $this->getBoundary($boundary[2], $bodyCharset, MimeType::M_HTML, $bodyEncoding);
                $body .= $this->encodeString($this->body, $bodyEncoding);
                $body .= $this->endOfLine;
                $body .= $this->attachAll(DispositionType::INLINE, $boundary[2]);
                $body .= $this->endOfLine;
                $body .= $this->endOfLine . '--' . $boundary[2] . '--' . $this->endOfLine;
                $body .= $this->attachAll(DispositionType::ATTACHMENT, $boundary[1]);
                break;
            default:
                $body .= $this->encodeString($this->body, $bodyEncoding);
        }

        if ($this->signKeyFile !== '') {
            // @todo implement
        }

        return $output;
    }

    /**
     * Normalize text
     *
     * Line break
     *
     * @param string $text Text to normalized
     * @param string $lb   Line break
     */
    private function normalizeText(string $text, string $lb = "\n") : string
    {
        return \str_replace(["\r\n", "\r", "\n"], $lb, $text);
    }

    /**
     * Generate a random id
     *
     * @return string
     *
     * @since 1.0.0
     */
    private function generatedId() : string
    {
        $rand = '';

        try {
            $rand = \random_bytes(32);
        } catch (\Throwable $t) {
            $rand = \hash('sha256', \uniqid((string) \mt_rand(), true), true);
        }

        return \base64_encode(\hash('sha256', $rand, true));
    }

    /**
     * Generate the mime header
     *
     * @param array $boundary Message boundary
     *
     * @return string
     *
     * @since 1.0.0
     */
    private function generateMimeHeader(array $boundary) : string
    {
        $mime        = '';
        $isMultipart = true;

        switch ($this->messageType) {
            case DispositionType::INLINE:
                $mime .= 'Content-Type:' . MimeType::M_RELATED . ';' . $this->endOfLine;
                $mime .= ' boundary="' . $boundary[0] . '"' . $this->endOfLine;
                break;
            case DispositionType::ATTACHMENT:
            case DispositionType::INLINE . '_' . DispositionType::ATTACHMENT:
            case DispositionType::ALT . '_' . DispositionType::ATTACHMENT:
            case DispositionType::ALT . '_' . DispositionType::INLINE . '_' . DispositionType::ATTACHMENT:
                $mime .= 'Content-Type:' . MimeType::M_MIXED . ';' . $this->endOfLine;
                $mime .= ' boundary="' . $boundary[0] . '"' . $this->endOfLine;
                break;
            case DispositionType::ALT:
            case DispositionType::ALT . '_' . DispositionType::INLINE:
                $mime .= 'Content-Type:' . MimeType::M_ALT . ';' . $this->endOfLine;
                $mime .= ' boundary="' . $boundary[0] . '"' . $this->endOfLine;
                break;
            default:
                $mime .= 'Content-Type:' . $this->contentType . '; charset=' . CharsetType::UTF_8 . ';' . $this->endOfLine;

                $isMultipart = false;
        }

        return $isMultipart && $this->encoding !== EncodingType::E_7BIT
            ? 'Content-Transfer-Encoding:' . $this->encoding . ';' . $this->endOfLine
            : $mime;
    }

    /**
     * Wrap text
     *
     * @param string $text   Text to wrap
     * @param int    $length Line length
     * @param bool   $quoted Is quoted
     *
     * @return string
     *
     * @since 1.0.0
     */
    private function wrapText(string $text, int $length, bool $quoted = false) : string
    {
        if ($length < 1 || $text === '') {
            return $text;
        }

        $softEndOfLine = $quoted ? ' =' . $this->endOfLine : $this->endOfLine;

        $text = $this->normalizeText($text, $this->endOfLine);
        $text = \rtrim($text, "\r\n");

        $lines = \explode($this->endOfLine, $text);

        $buffer = '';
        $output = '';
        $crlfLength = \strlen($this->endOfLine);
        $first = true;
        $isUTF8 = $this->charset === CharsetType::UTF_8;

        foreach ($lines as $line) {
            $words = \explode(' ', $line);

            foreach ($words as $word) {
                if ($quoted && \strlen($word) > $length) {
                    $spaces = $length - \strlen($buffer) - $crlfLength;

                    if ($first) {
                        if ($spaces > 20) {
                            $len = $spaces;
                            if ($isUTF8) {
                                $len = MbStringUtils::utf8CharBoundary($word, $len);
                            } elseif ('=' === \substr($word, $len - 1, 1)) {
                                --$len;
                            } elseif ('=' === \substr($word, $len - 2, 1)) {
                                $len -= 2;
                            }

                            $part = \substr($word, 0, $len);
                            $word = \substr($word, $len);
                            $buffer .= ' ' . $part;
                            $output .= $buffer . '=' . $this->endOfLine;
                        } else {
                            $output .= $buffer . $softEndOfLine;
                        }

                        $buffer = '';
                    }

                    while ($word !== '') {
                        if ($length < 1) {
                            break;
                        }

                        $len = $length;

                        if ($isUTF8) {
                            $len = MbStringUtils::utf8CharBoundary($word, $len);
                        } elseif ('=' === \substr($word, $len - 1, 1)) {
                            --$len;
                        } elseif ('=' === \substr($word, $len - 2, 1)) {
                            $len -= 2;
                        }

                        $part = \substr($word, 0, $len);
                        $word = \substr($word, $len);

                        if ($word !== '') {
                            $output .= $part . '=' . $this->endOfLine;
                        } else {
                            $buffer = $part;
                        }
                    }
                } else {
                    $oldBuf  = $buffer;
                    $buffer .= $word . ' ';

                    if (\strlen($buffer) > $length) {
                        $output .= \rtrim($oldBuf) . $softEndOfLine;
                        $buffer  = $word;
                    }
                }
            }

            $output .= \rtrim($buffer) . $this->endOfLine;
        }

        return $output;
    }

    /**
     * Render the boundary
     *
     * @param string $boundary    Boundary identifier
     * @param string $charset     Charset
     * @param string $contentType ContentType
     * @param string $encoding    Encoding
     *
     * @return string
     *
     * @since 1.0.0
     */
    private function getBoundary(string $boundary, string $charset = null, string $contentType = null, string $encoding = null) : string
    {
        $boundary    = '';
        $charset     = empty($charset) ? $this->charset : $charset;
        $contentType = empty($contentType) ? $this->contentType : $contentType;
        $encoding    = empty($encoding) ? $this->encoding : $encoding;

        $boundary .= '--' . $boundary . $this->endOfLine;
        $boundary .= 'Content-Type: ' . $contentType . '; charset=' . $charset . $this->endOfLine;

        if ($encoding !== EncodingType::E_7BIT) {
            $boundary .= 'Content-Transfer-Encoding: ' . $encoding . $this->endOfLine;
        }

        return $boundary . $this->endOfLine;
    }

    /**
     * Encode a string
     *
     * @param string $text     Text to encode
     * @param string $encoding Encoding to use
     *
     * @return string
     *
     * @since 1.0.0
     */
    private function encodeString(string $text, string $encoding = EncodingType::E_BASE64) : string
    {
        $encoded = '';
        if ($encoding === EncodingType::E_BASE64) {
            $encoded = \chunk_split(\base64_encode($text), 76, $this->endOfLine);
        } elseif ($encoding === EncodingType::E_7BIT || $encoding === EncodingType::E_8BIT) {
            $encoded = $this->normalizeText($text, $this->endOfLine);

            if (\substr($encoded, -\strlen($this->endOfLine)) !== $this->endOfLine) {
                $encoded .= $this->endOfLine;
            }
        } elseif ($encoding === EncodingType::E_BINARY) {
            $encoded = $text;
        } elseif ($encoded === EncodingType::E_QUOTED) {
            $encoded = $this->encodeQuoted($text);
        }

        return $encoded;
    }

    private function attachAll(string $disposition, string $boundary) : string
    {
        $mime = [];
        $cid  = [];
        $incl = [];

        foreach ($this->attachment as $attach) {
            if ($attach['disposition'] === $disposition) {
                $text = '';
                $path = '';
                $isString = $attach['string'];

                if (!empty($isString)) {
                    $text = $attach['path'];
                } else {
                    $path = $attach['path'];
                }

                $hash = \hash('sha256', \serialize($attach));
                if (\in_array($hash, $incl, true)) {
                    continue;
                }

                $incl[] = $hash;

                if ($attach['disposition'] && isset($cid[$attach['id']])) {
                    continue;
                }

                $cid[$attach['id']] = true;
                $mime[] = '--' . $boundary . $this->endOfLine;
                $mime[] = !empty($attach['name'])
                    ? 'Content-Type: ' . $attach['type'] . '; name="' . $this->encodeHeader($this->secureHeader($attach['name'])) . '"' . $this->endOfLine
                    : 'Content-Type: ' . $attach['type'] . $this->endOfLine;

                if ($attach['encoding'] !== EncodingType::E_7BIT) {
                    $mime[] = 'Content-Transfer-Encoding: ' . $attach['encoding'] . $this->endOfLine;
                }

            }
        }

        $mime[] = '--' . $boundary . '--' . $this->endOfLine;

        return \implode('', $mime);
    }

    private function encodeQuoted(): void
    {

    }

    private function encodeHeader(): void
    {

    }
}
