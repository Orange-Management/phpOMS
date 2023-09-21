<?php
/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package    phpOMS\Utils\Parser\Markdown
 * @license    Original license Emanuil Rusev, erusev.com (MIT)
 * @version    1.0.0
 * @link       https://jingga.app
 */
declare(strict_types=1);

namespace phpOMS\Utils\Parser\Markdown;

use phpOMS\Uri\UriFactory;

/**
 * Markdown parser class.
 *
 * @package    phpOMS\Utils\Parser\Markdown
 * @license    Original license Emanuil Rusev, erusev.com (MIT)
 * @link       https://jingga.app
 * @since      1.0.0
 */
class Markdown
{
    # ~

    const version = '1.8.0-beta-7';

    private array $options = [];

    # ~

    private string $idToc = '';

    public function __construct(array $params = [])
    {
        $this->options = $params;

        $this->options['toc'] = $this->options['toc'] ?? false;

        // Marks
        $state = $this->options['mark'] ?? true;
        if ($state !== false) {
            $this->InlineTypes['='][] = 'mark';
            $this->inlineMarkerList .= '=';
        }

        // Keystrokes
        $state = $this->options['keystrokes'] ?? true;
        if ($state !== false) {
            $this->InlineTypes['['][] = 'Keystrokes';
            $this->inlineMarkerList .= '[';
        }

        // Inline Math
        $state = $this->options['math'] ?? false;
        if ($state !== false) {
            $this->InlineTypes['\\'][] = 'Math';
            $this->inlineMarkerList .= '\\';
            $this->InlineTypes['$'][] = 'Math';
            $this->inlineMarkerList .= '$';
        }

        // Superscript
        $state = $this->options['sup'] ?? false;
        if ($state !== false) {
            $this->InlineTypes['^'][] = 'Superscript';
            $this->inlineMarkerList .= '^';
        }

        // Subscript
        $state = $this->options['sub'] ?? false;
        if ($state !== false) {
            $this->InlineTypes['~'][] = 'Subscript';
        }

        // Emojis
        $state = $this->options['emojis'] ?? true;
        if ($state !== false) {
            $this->InlineTypes[':'][] = 'Emojis';
            $this->inlineMarkerList .= ':';
        }

        // Typographer
        $state = $this->options['typographer'] ?? false;
        if ($state !== false) {
            $this->InlineTypes['('][] = 'Typographer';
            $this->inlineMarkerList .= '(';
            $this->InlineTypes['.'][] = 'Typographer';
            $this->inlineMarkerList .= '.';
            $this->InlineTypes['+'][] = 'Typographer';
            $this->inlineMarkerList .= '+';
            $this->InlineTypes['!'][] = 'Typographer';
            $this->inlineMarkerList .= '!';
            $this->InlineTypes['?'][] = 'Typographer';
            $this->inlineMarkerList .= '?';
        }

        // Smartypants
        $state = $this->options['smarty'] ?? false;
        if ($state !== false) {
            $this->InlineTypes['<'][] = 'Smartypants';
            $this->inlineMarkerList .= '<';
            $this->InlineTypes['>'][] = 'Smartypants';
            $this->inlineMarkerList .= '>';
            $this->InlineTypes['-'][] = 'Smartypants';
            $this->inlineMarkerList .= '-';
            $this->InlineTypes['.'][] = 'Smartypants';
            $this->inlineMarkerList .= '.';
            $this->InlineTypes["'"][] = 'Smartypants';
            $this->inlineMarkerList .= "'";
            $this->InlineTypes['"'][] = 'Smartypants';
            $this->inlineMarkerList .= '"';
            $this->InlineTypes['`'][] = 'Smartypants';
            $this->inlineMarkerList .= '`';
        }

        /*
         * Blocks
         * ------------------------------------------------------------------------
         */

        // Block Math
        $state = $this->options['math'] ?? false;
        if ($state !== false) {
            $this->BlockTypes['\\'][] = 'Math';
            $this->BlockTypes['$'][] = 'Math';
        }

        // Task
        $state = $this->options['lists']['tasks'] ?? true;
        if ($state !== false) {
            $this->BlockTypes['['][] = 'Checkbox';
        }
    }

    function textParent($text)
    {
        $Elements = $this->textElements($text);

        # convert to markup
        $markup = $this->elements($Elements);

        # trim line breaks
        $markup = trim($markup, "\n");

        # merge consecutive dl elements

        $markup = preg_replace('/<\/dl>\s+<dl>\s+/', '', $markup);

        # add footnotes

        if (isset($this->DefinitionData['Footnote']))
        {
            $Element = $this->buildFootnoteElement();

            $markup .= "\n" . $this->element($Element);
        }

        return $markup;
    }

    /**
     * Parses the given markdown string to an HTML string but it leaves the ToC
     * tag as is. It's an alias of the parent method "\DynamicParent::text()".
     */
    public function body($text): string
    {
        $text = $this->encodeTagToHash($text);  // Escapes ToC tag temporary
        $html = $this->textParent($text);     // Parses the markdown text

        return $this->decodeTagFromHash($html); // Unescape the ToC tag
    }

    /**
     * Parses markdown string to HTML and also the "[toc]" tag as well.
     * It overrides the parent method: \Parsedown::text().
     */
    public function text($text)
    {
        // Parses the markdown text except the ToC tag. This also searches
        // the list of contents and available to get from "contentsList()"
        // method.
        $html = $this->body($text);

        if (isset($this->options['toc']) && false == $this->options['toc']) {
            return $html;
        }

        $tagOrigin = $this->getTagToC();

        if (strpos($text, $tagOrigin) === false) {
            return $html;
        }

        $tocData = $this->contentsList();
        $tocId = $this->getIdAttributeToC();
        $needle = '<p>'.$tagOrigin.'</p>';
        $replace = "<div id=\"{$tocId}\">{$tocData}</div>";

        return str_replace($needle, $replace, $html);
    }

    /**
     * Returns the parsed ToC.
     *
     * @param string $typeReturn Type of the return format. "html" or "json".
     *
     * @return string HTML/JSON string of ToC
     */
    public function contentsList($typeReturn = 'html')
    {
        if ('html' === strtolower($typeReturn)) {
            $result = '';
            if (!empty($this->contentsListString)) {
                // Parses the ToC list in markdown to HTML
                $result = $this->body($this->contentsListString);
            }

            return $result;
        }

        if ('json' === strtolower($typeReturn)) {
            return json_encode($this->contentsListArray);
        }

        // Forces to return ToC as "html"
        error_log(
            'Unknown return type given while parsing ToC.'
            .' At: '.__FUNCTION__.'() '
            .' in Line:'.__LINE__.' (Using default type)'
        );

        return $this->contentsList('html');
    }

    /**
     * ------------------------------------------------------------------------
     * Inline
     * ------------------------------------------------------------------------.
     */

    // inlineCode
    protected function inlineCode($excerpt)
    {
        $codeSnippets = $this->options['code']['inline'] ?? true;
        $codeMain = $this->options['code'] ?? true;

        if ($codeSnippets === true and $codeMain === true) {
            return $this->inlineCodeParent($excerpt);
        }
    }

    protected function inlineEmailTag($excerpt)
    {
        $mainState = $this->options['links'] ?? true;
        $state = $this->options['links']['email_links'] ?? true;

        if ($mainState and $state) {
            return $this->inlineEmailTagParent($excerpt);
        }
    }

    protected function inlineEmphasis($excerpt)
    {
        $state = $this->options['emphasis'] ?? true;
        if ($state) {
            return $this->inlineEmphasisParent($excerpt);
        }
    }

    protected function inlineImage($excerpt)
    {
        $state = $this->options['images'] ?? true;
        if ($state) {
            return $this->inlineImageParent($excerpt);
        }
    }

    protected function inlineLink($excerpt)
    {
        $state = $this->options['links'] ?? true;
        if ($state) {
            return $this->inlineLinkBase($excerpt);
        }
    }

    protected function inlineMarkup($excerpt)
    {
        $state = $this->options['markup'] ?? true;
        if ($state) {
            return $this->inlineMarkupBase($excerpt);
        }
    }

    protected function inlineStrikethrough($excerpt)
    {
        $state = $this->options['strikethroughs'] ?? true;
        if ($state) {
            return $this->inlineStrikethroughBase($excerpt);
        }
    }

    protected function inlineUrl($excerpt)
    {
        $state = $this->options['links'] ?? true;
        if ($state) {
            return $this->inlineUrlBase($excerpt);
        }
    }

    protected function inlineUrlTag($excerpt)
    {
        $state = $this->options['links'] ?? true;
        if ($state) {
            return $this->inlineUrlTagBase($excerpt);
        }
    }

    protected function inlineEmojis($excerpt)
    {
        $emojiMap = [
            ':smile:' => '😄', ':laughing:' => '😆', ':blush:' => '😊', ':smiley:' => '😃',
            ':relaxed:' => '☺️', ':smirk:' => '😏', ':heart_eyes:' => '😍', ':kissing_heart:' => '😘',
            ':kissing_closed_eyes:' => '😚', ':flushed:' => '😳', ':relieved:' => '😌', ':satisfied:' => '😆',
            ':grin:' => '😁', ':wink:' => '😉', ':stuck_out_tongue_winking_eye:' => '😜', ':stuck_out_tongue_closed_eyes:' => '😝',
            ':grinning:' => '😀', ':kissing:' => '😗', ':kissing_smiling_eyes:' => '😙', ':stuck_out_tongue:' => '😛',
            ':sleeping:' => '😴', ':worried:' => '😟', ':frowning:' => '😦', ':anguished:' => '😧',
            ':open_mouth:' => '😮', ':grimacing:' => '😬', ':confused:' => '😕', ':hushed:' => '😯',
            ':expressionless:' => '😑', ':unamused:' => '😒', ':sweat_smile:' => '😅', ':sweat:' => '😓',
            ':disappointed_relieved:' => '😥', ':weary:' => '😩', ':pensive:' => '😔', ':disappointed:' => '😞',
            ':confounded:' => '😖', ':fearful:' => '😨', ':cold_sweat:' => '😰', ':persevere:' => '😣',
            ':cry:' => '😢', ':sob:' => '😭', ':joy:' => '😂', ':astonished:' => '😲',
            ':scream:' => '😱', ':tired_face:' => '😫', ':angry:' => '😠', ':rage:' => '😡',
            ':triumph:' => '😤', ':sleepy:' => '😪', ':yum:' => '😋', ':mask:' => '😷',
            ':sunglasses:' => '😎', ':dizzy_face:' => '😵', ':imp:' => '👿', ':smiling_imp:' => '😈',
            ':neutral_face:' => '😐', ':no_mouth:' => '😶', ':innocent:' => '😇', ':alien:' => '👽',
            ':yellow_heart:' => '💛', ':blue_heart:' => '💙', ':purple_heart:' => '💜', ':heart:' => '❤️',
            ':green_heart:' => '💚', ':broken_heart:' => '💔', ':heartbeat:' => '💓', ':heartpulse:' => '💗',
            ':two_hearts:' => '💕', ':revolving_hearts:' => '💞', ':cupid:' => '💘', ':sparkling_heart:' => '💖',
            ':sparkles:' => '✨', ':star:' => '⭐️', ':star2:' => '🌟', ':dizzy:' => '💫',
            ':boom:' => '💥', ':collision:' => '💥', ':anger:' => '💢', ':exclamation:' => '❗️',
            ':question:' => '❓', ':grey_exclamation:' => '❕', ':grey_question:' => '❔', ':zzz:' => '💤',
            ':dash:' => '💨', ':sweat_drops:' => '💦', ':notes:' => '🎶', ':musical_note:' => '🎵',
            ':fire:' => '🔥', ':hankey:' => '💩', ':poop:' => '💩', ':shit:' => '💩',
            ':+1:' => '👍', ':thumbsup:' => '👍', ':-1:' => '👎', ':thumbsdown:' => '👎',
            ':ok_hand:' => '👌', ':punch:' => '👊', ':facepunch:' => '👊', ':fist:' => '✊',
            ':v:' => '✌️', ':wave:' => '👋', ':hand:' => '✋', ':raised_hand:' => '✋',
            ':open_hands:' => '👐', ':point_up:' => '☝️', ':point_down:' => '👇', ':point_left:' => '👈',
            ':point_right:' => '👉', ':raised_hands:' => '🙌', ':pray:' => '🙏', ':point_up_2:' => '👆',
            ':clap:' => '👏', ':muscle:' => '💪', ':metal:' => '🤘', ':fu:' => '🖕',
            ':walking:' => '🚶', ':runner:' => '🏃', ':running:' => '🏃', ':couple:' => '👫',
            ':family:' => '👪', ':two_men_holding_hands:' => '👬', ':two_women_holding_hands:' => '👭', ':dancer:' => '💃',
            ':dancers:' => '👯', ':ok_woman:' => '🙆', ':no_good:' => '🙅', ':information_desk_person:' => '💁',
            ':raising_hand:' => '🙋', ':bride_with_veil:' => '👰', ':person_with_pouting_face:' => '🙎', ':person_frowning:' => '🙍',
            ':bow:' => '🙇', ':couple_with_heart:' => '💑', ':massage:' => '💆', ':haircut:' => '💇',
            ':nail_care:' => '💅', ':boy:' => '👦', ':girl:' => '👧', ':woman:' => '👩',
            ':man:' => '👨', ':baby:' => '👶', ':older_woman:' => '👵', ':older_man:' => '👴',
            ':person_with_blond_hair:' => '👱', ':man_with_gua_pi_mao:' => '👲', ':man_with_turban:' => '👳', ':construction_worker:' => '👷',
            ':cop:' => '👮', ':angel:' => '👼', ':princess:' => '👸', ':smiley_cat:' => '😺',
            ':smile_cat:' => '😸', ':heart_eyes_cat:' => '😻', ':kissing_cat:' => '😽', ':smirk_cat:' => '😼',
            ':scream_cat:' => '🙀', ':crying_cat_face:' => '😿', ':joy_cat:' => '😹', ':pouting_cat:' => '😾',
            ':japanese_ogre:' => '👹', ':japanese_goblin:' => '👺', ':see_no_evil:' => '🙈', ':hear_no_evil:' => '🙉',
            ':speak_no_evil:' => '🙊', ':guardsman:' => '💂', ':skull:' => '💀', ':feet:' => '🐾',
            ':lips:' => '👄', ':kiss:' => '💋', ':droplet:' => '💧', ':ear:' => '👂',
            ':eyes:' => '👀', ':nose:' => '👃', ':tongue:' => '👅', ':love_letter:' => '💌',
            ':bust_in_silhouette:' => '👤', ':busts_in_silhouette:' => '👥', ':speech_balloon:' => '💬', ':thought_balloon:' => '💭',
            ':sunny:' => '☀️', ':umbrella:' => '☔️', ':cloud:' => '☁️', ':snowflake:' => '❄️',
            ':snowman:' => '⛄️', ':zap:' => '⚡️', ':cyclone:' => '🌀', ':foggy:' => '🌁',
            ':ocean:' => '🌊', ':cat:' => '🐱', ':dog:' => '🐶', ':mouse:' => '🐭',
            ':hamster:' => '🐹', ':rabbit:' => '🐰', ':wolf:' => '🐺', ':frog:' => '🐸',
            ':tiger:' => '🐯', ':koala:' => '🐨', ':bear:' => '🐻', ':pig:' => '🐷',
            ':pig_nose:' => '🐽', ':cow:' => '🐮', ':boar:' => '🐗', ':monkey_face:' => '🐵',
            ':monkey:' => '🐒', ':horse:' => '🐴', ':racehorse:' => '🐎', ':camel:' => '🐫',
            ':sheep:' => '🐑', ':elephant:' => '🐘', ':panda_face:' => '🐼', ':snake:' => '🐍',
            ':bird:' => '🐦', ':baby_chick:' => '🐤', ':hatched_chick:' => '🐥', ':hatching_chick:' => '🐣',
            ':chicken:' => '🐔', ':penguin:' => '🐧', ':turtle:' => '🐢', ':bug:' => '🐛',
            ':honeybee:' => '🐝', ':ant:' => '🐜', ':beetle:' => '🐞', ':snail:' => '🐌',
            ':octopus:' => '🐙', ':tropical_fish:' => '🐠', ':fish:' => '🐟', ':whale:' => '🐳',
            ':whale2:' => '🐋', ':dolphin:' => '🐬', ':cow2:' => '🐄', ':ram:' => '🐏',
            ':rat:' => '🐀', ':water_buffalo:' => '🐃', ':tiger2:' => '🐅', ':rabbit2:' => '🐇',
            ':dragon:' => '🐉', ':goat:' => '🐐', ':rooster:' => '🐓', ':dog2:' => '🐕',
            ':pig2:' => '🐖', ':mouse2:' => '🐁', ':ox:' => '🐂', ':dragon_face:' => '🐲',
            ':blowfish:' => '🐡', ':crocodile:' => '🐊', ':dromedary_camel:' => '🐪', ':leopard:' => '🐆',
            ':cat2:' => '🐈', ':poodle:' => '🐩', ':crab' => '🦀', ':paw_prints:' => '🐾', ':bouquet:' => '💐',
            ':cherry_blossom:' => '🌸', ':tulip:' => '🌷', ':four_leaf_clover:' => '🍀', ':rose:' => '🌹',
            ':sunflower:' => '🌻', ':hibiscus:' => '🌺', ':maple_leaf:' => '🍁', ':leaves:' => '🍃',
            ':fallen_leaf:' => '🍂', ':herb:' => '🌿', ':mushroom:' => '🍄', ':cactus:' => '🌵',
            ':palm_tree:' => '🌴', ':evergreen_tree:' => '🌲', ':deciduous_tree:' => '🌳', ':chestnut:' => '🌰',
            ':seedling:' => '🌱', ':blossom:' => '🌼', ':ear_of_rice:' => '🌾', ':shell:' => '🐚',
            ':globe_with_meridians:' => '🌐', ':sun_with_face:' => '🌞', ':full_moon_with_face:' => '🌝', ':new_moon_with_face:' => '🌚',
            ':new_moon:' => '🌑', ':waxing_crescent_moon:' => '🌒', ':first_quarter_moon:' => '🌓', ':waxing_gibbous_moon:' => '🌔',
            ':full_moon:' => '🌕', ':waning_gibbous_moon:' => '🌖', ':last_quarter_moon:' => '🌗', ':waning_crescent_moon:' => '🌘',
            ':last_quarter_moon_with_face:' => '🌜', ':first_quarter_moon_with_face:' => '🌛', ':moon:' => '🌔', ':earth_africa:' => '🌍',
            ':earth_americas:' => '🌎', ':earth_asia:' => '🌏', ':volcano:' => '🌋', ':milky_way:' => '🌌',
            ':partly_sunny:' => '⛅️', ':bamboo:' => '🎍', ':gift_heart:' => '💝', ':dolls:' => '🎎',
            ':school_satchel:' => '🎒', ':mortar_board:' => '🎓', ':flags:' => '🎏', ':fireworks:' => '🎆',
            ':sparkler:' => '🎇', ':wind_chime:' => '🎐', ':rice_scene:' => '🎑', ':jack_o_lantern:' => '🎃',
            ':ghost:' => '👻', ':santa:' => '🎅', ':christmas_tree:' => '🎄', ':gift:' => '🎁',
            ':bell:' => '🔔', ':no_bell:' => '🔕', ':tanabata_tree:' => '🎋', ':tada:' => '🎉',
            ':confetti_ball:' => '🎊', ':balloon:' => '🎈', ':crystal_ball:' => '🔮', ':cd:' => '💿',
            ':dvd:' => '📀', ':floppy_disk:' => '💾', ':camera:' => '📷', ':video_camera:' => '📹',
            ':movie_camera:' => '🎥', ':computer:' => '💻', ':tv:' => '📺', ':iphone:' => '📱',
            ':phone:' => '☎️', ':telephone:' => '☎️', ':telephone_receiver:' => '📞', ':pager:' => '📟',
            ':fax:' => '📠', ':minidisc:' => '💽', ':vhs:' => '📼', ':sound:' => '🔉',
            ':speaker:' => '🔈', ':mute:' => '🔇', ':loudspeaker:' => '📢', ':mega:' => '📣',
            ':hourglass:' => '⌛️', ':hourglass_flowing_sand:' => '⏳', ':alarm_clock:' => '⏰', ':watch:' => '⌚️',
            ':radio:' => '📻', ':satellite:' => '📡', ':loop:' => '➿', ':mag:' => '🔍',
            ':mag_right:' => '🔎', ':unlock:' => '🔓', ':lock:' => '🔒', ':lock_with_ink_pen:' => '🔏',
            ':closed_lock_with_key:' => '🔐', ':key:' => '🔑', ':bulb:' => '💡', ':flashlight:' => '🔦',
            ':high_brightness:' => '🔆', ':low_brightness:' => '🔅', ':electric_plug:' => '🔌', ':battery:' => '🔋',
            ':calling:' => '📲', ':email:' => '✉️', ':mailbox:' => '📫', ':postbox:' => '📮',
            ':bath:' => '🛀', ':bathtub:' => '🛁', ':shower:' => '🚿', ':toilet:' => '🚽',
            ':wrench:' => '🔧', ':nut_and_bolt:' => '🔩', ':hammer:' => '🔨', ':seat:' => '💺',
            ':moneybag:' => '💰', ':yen:' => '💴', ':dollar:' => '💵', ':pound:' => '💷',
            ':euro:' => '💶', ':credit_card:' => '💳', ':money_with_wings:' => '💸', ':e-mail:' => '📧',
            ':inbox_tray:' => '📥', ':outbox_tray:' => '📤', ':envelope:' => '✉️', ':incoming_envelope:' => '📨',
            ':postal_horn:' => '📯', ':mailbox_closed:' => '📪', ':mailbox_with_mail:' => '📬', ':mailbox_with_no_mail:' => '📭',
            ':door:' => '🚪', ':smoking:' => '🚬', ':bomb:' => '💣', ':gun:' => '🔫',
            ':hocho:' => '🔪', ':pill:' => '💊', ':syringe:' => '💉', ':page_facing_up:' => '📄',
            ':page_with_curl:' => '📃', ':bookmark_tabs:' => '📑', ':bar_chart:' => '📊', ':chart_with_upwards_trend:' => '📈',
            ':chart_with_downwards_trend:' => '📉', ':scroll:' => '📜', ':clipboard:' => '📋', ':calendar:' => '📆',
            ':date:' => '📅', ':card_index:' => '📇', ':file_folder:' => '📁', ':open_file_folder:' => '📂',
            ':scissors:' => '✂️', ':pushpin:' => '📌', ':paperclip:' => '📎', ':black_nib:' => '✒️',
            ':pencil2:' => '✏️', ':straight_ruler:' => '📏', ':triangular_ruler:' => '📐', ':closed_book:' => '📕',
            ':green_book:' => '📗', ':blue_book:' => '📘', ':orange_book:' => '📙', ':notebook:' => '📓',
            ':notebook_with_decorative_cover:' => '📔', ':ledger:' => '📒', ':books:' => '📚', ':bookmark:' => '🔖',
            ':name_badge:' => '📛', ':microscope:' => '🔬', ':telescope:' => '🔭', ':newspaper:' => '📰',
            ':football:' => '🏈', ':basketball:' => '🏀', ':soccer:' => '⚽️', ':baseball:' => '⚾️',
            ':tennis:' => '🎾', ':8ball:' => '🎱', ':rugby_football:' => '🏉', ':bowling:' => '🎳',
            ':golf:' => '⛳️', ':mountain_bicyclist:' => '🚵', ':bicyclist:' => '🚴', ':horse_racing:' => '🏇',
            ':snowboarder:' => '🏂', ':swimmer:' => '🏊', ':surfer:' => '🏄', ':ski:' => '🎿',
            ':spades:' => '♠️', ':hearts:' => '♥️', ':clubs:' => '♣️', ':diamonds:' => '♦️',
            ':gem:' => '💎', ':ring:' => '💍', ':trophy:' => '🏆', ':musical_score:' => '🎼',
            ':musical_keyboard:' => '🎹', ':violin:' => '🎻', ':space_invader:' => '👾', ':video_game:' => '🎮',
            ':black_joker:' => '🃏', ':flower_playing_cards:' => '🎴', ':game_die:' => '🎲', ':dart:' => '🎯',
            ':mahjong:' => '🀄️', ':clapper:' => '🎬', ':memo:' => '📝', ':pencil:' => '📝',
            ':book:' => '📖', ':art:' => '🎨', ':microphone:' => '🎤', ':headphones:' => '🎧',
            ':trumpet:' => '🎺', ':saxophone:' => '🎷', ':guitar:' => '🎸', ':shoe:' => '👞',
            ':sandal:' => '👡', ':high_heel:' => '👠', ':lipstick:' => '💄', ':boot:' => '👢',
            ':shirt:' => '👕', ':tshirt:' => '👕', ':necktie:' => '👔', ':womans_clothes:' => '👚',
            ':dress:' => '👗', ':running_shirt_with_sash:' => '🎽', ':jeans:' => '👖', ':kimono:' => '👘',
            ':bikini:' => '👙', ':ribbon:' => '🎀', ':tophat:' => '🎩', ':crown:' => '👑',
            ':womans_hat:' => '👒', ':mans_shoe:' => '👞', ':closed_umbrella:' => '🌂', ':briefcase:' => '💼',
            ':handbag:' => '👜', ':pouch:' => '👝', ':purse:' => '👛', ':eyeglasses:' => '👓',
            ':fishing_pole_and_fish:' => '🎣', ':coffee:' => '☕️', ':tea:' => '🍵', ':sake:' => '🍶',
            ':baby_bottle:' => '🍼', ':beer:' => '🍺', ':beers:' => '🍻', ':cocktail:' => '🍸',
            ':tropical_drink:' => '🍹', ':wine_glass:' => '🍷', ':fork_and_knife:' => '🍴', ':pizza:' => '🍕',
            ':hamburger:' => '🍔', ':fries:' => '🍟', ':poultry_leg:' => '🍗', ':meat_on_bone:' => '🍖',
            ':spaghetti:' => '🍝', ':curry:' => '🍛', ':fried_shrimp:' => '🍤', ':bento:' => '🍱',
            ':sushi:' => '🍣', ':fish_cake:' => '🍥', ':rice_ball:' => '🍙', ':rice_cracker:' => '🍘',
            ':rice:' => '🍚', ':ramen:' => '🍜', ':stew:' => '🍲', ':oden:' => '🍢',
            ':dango:' => '🍡', ':egg:' => '🥚', ':bread:' => '🍞', ':doughnut:' => '🍩',
            ':custard:' => '🍮', ':icecream:' => '🍦', ':ice_cream:' => '🍨', ':shaved_ice:' => '🍧',
            ':birthday:' => '🎂', ':cake:' => '🍰', ':cookie:' => '🍪', ':chocolate_bar:' => '🍫',
            ':candy:' => '🍬', ':lollipop:' => '🍭', ':honey_pot:' => '🍯', ':apple:' => '🍎',
            ':green_apple:' => '🍏', ':tangerine:' => '🍊', ':lemon:' => '🍋', ':cherries:' => '🍒',
            ':grapes:' => '🍇', ':watermelon:' => '🍉', ':strawberry:' => '🍓', ':peach:' => '🍑',
            ':melon:' => '🍈', ':banana:' => '🍌', ':pear:' => '🍐', ':pineapple:' => '🍍',
            ':sweet_potato:' => '🍠', ':eggplant:' => '🍆', ':tomato:' => '🍅', ':corn:' => '🌽',
            ':house:' => '🏠', ':house_with_garden:' => '🏡', ':school:' => '🏫', ':office:' => '🏢',
            ':post_office:' => '🏣', ':hospital:' => '🏥', ':bank:' => '🏦', ':convenience_store:' => '🏪',
            ':love_hotel:' => '🏩', ':hotel:' => '🏨', ':wedding:' => '💒', ':church:' => '⛪️',
            ':department_store:' => '🏬', ':european_post_office:' => '🏤', ':city_sunrise:' => '🌇', ':city_sunset:' => '🌆',
            ':japanese_castle:' => '🏯', ':european_castle:' => '🏰', ':tent:' => '⛺️', ':factory:' => '🏭',
            ':tokyo_tower:' => '🗼', ':japan:' => '🗾', ':mount_fuji:' => '🗻', ':sunrise_over_mountains:' => '🌄',
            ':sunrise:' => '🌅', ':stars:' => '🌠', ':statue_of_liberty:' => '🗽', ':bridge_at_night:' => '🌉',
            ':carousel_horse:' => '🎠', ':rainbow:' => '🌈', ':ferris_wheel:' => '🎡', ':fountain:' => '⛲️',
            ':roller_coaster:' => '🎢', ':ship:' => '🚢', ':speedboat:' => '🚤', ':boat:' => '⛵️',
            ':sailboat:' => '⛵️', ':rowboat:' => '🚣', ':anchor:' => '⚓️', ':rocket:' => '🚀',
            ':airplane:' => '✈️', ':helicopter:' => '🚁', ':steam_locomotive:' => '🚂', ':tram:' => '🚊',
            ':mountain_railway:' => '🚞', ':bike:' => '🚲', ':aerial_tramway:' => '🚡', ':suspension_railway:' => '🚟',
            ':mountain_cableway:' => '🚠', ':tractor:' => '🚜', ':blue_car:' => '🚙', ':oncoming_automobile:' => '🚘',
            ':car:' => '🚗', ':red_car:' => '🚗', ':taxi:' => '🚕', ':oncoming_taxi:' => '🚖',
            ':articulated_lorry:' => '🚛', ':bus:' => '🚌', ':oncoming_bus:' => '🚍', ':rotating_light:' => '🚨',
            ':police_car:' => '🚓', ':oncoming_police_car:' => '🚔', ':fire_engine:' => '🚒', ':ambulance:' => '🚑',
            ':minibus:' => '🚐', ':truck:' => '🚚', ':train:' => '🚋', ':station:' => '🚉',
            ':train2:' => '🚆', ':bullettrain_front:' => '🚅', ':bullettrain_side:' => '🚄', ':light_rail:' => '🚈',
            ':monorail:' => '🚝', ':railway_car:' => '🚃', ':trolleybus:' => '🚎', ':ticket:' => '🎫',
            ':fuelpump:' => '⛽️', ':vertical_traffic_light:' => '🚦', ':traffic_light:' => '🚥', ':warning:' => '⚠️',
            ':construction:' => '🚧', ':beginner:' => '🔰', ':atm:' => '🏧', ':slot_machine:' => '🎰',
            ':busstop:' => '🚏', ':barber:' => '💈', ':hotsprings:' => '♨️', ':checkered_flag:' => '🏁',
            ':crossed_flags:' => '🎌', ':izakaya_lantern:' => '🏮', ':moyai:' => '🗿', ':circus_tent:' => '🎪',
            ':performing_arts:' => '🎭', ':round_pushpin:' => '📍', ':triangular_flag_on_post:' => '🚩', ':jp:' => '🇯🇵',
            ':kr:' => '🇰🇷', ':cn:' => '🇨🇳', ':us:' => '🇺🇸', ':fr:' => '🇫🇷',
            ':es:' => '🇪🇸', ':it:' => '🇮🇹', ':ru:' => '🇷🇺', ':gb:' => '🇬🇧',
            ':uk:' => '🇬🇧', ':de:' => '🇩🇪', ':one:' => '1️⃣', ':two:' => '2️⃣',
            ':three:' => '3️⃣', ':four:' => '4️⃣', ':five:' => '5️⃣', ':six:' => '6️⃣',
            ':seven:' => '7️⃣', ':eight:' => '8️⃣', ':nine:' => '9️⃣', ':keycap_ten:' => '🔟',
            ':1234:' => '🔢', ':zero:' => '0️⃣', ':hash:' => '#️⃣', ':symbols:' => '🔣',
            ':arrow_backward:' => '◀️', ':arrow_down:' => '⬇️', ':arrow_forward:' => '▶️', ':arrow_left:' => '⬅️',
            ':capital_abcd:' => '🔠', ':abcd:' => '🔡', ':abc:' => '🔤', ':arrow_lower_left:' => '↙️',
            ':arrow_lower_right:' => '↘️', ':arrow_right:' => '➡️', ':arrow_up:' => '⬆️', ':arrow_upper_left:' => '↖️',
            ':arrow_upper_right:' => '↗️', ':arrow_double_down:' => '⏬', ':arrow_double_up:' => '⏫', ':arrow_down_small:' => '🔽',
            ':arrow_heading_down:' => '⤵️', ':arrow_heading_up:' => '⤴️', ':leftwards_arrow_with_hook:' => '↩️', ':arrow_right_hook:' => '↪️',
            ':left_right_arrow:' => '↔️', ':arrow_up_down:' => '↕️', ':arrow_up_small:' => '🔼', ':arrows_clockwise:' => '🔃',
            ':arrows_counterclockwise:' => '🔄', ':rewind:' => '⏪', ':fast_forward:' => '⏩', ':information_source:' => 'ℹ️',
            ':ok:' => '🆗', ':twisted_rightwards_arrows:' => '🔀', ':repeat:' => '🔁', ':repeat_one:' => '🔂',
            ':new:' => '🆕', ':top:' => '🔝', ':up:' => '🆙', ':cool:' => '🆒',
            ':free:' => '🆓', ':ng:' => '🆖', ':cinema:' => '🎦', ':koko:' => '🈁',
            ':signal_strength:' => '📶', ':u5272:' => '🈹', ':u5408:' => '🈴', ':u55b6:' => '🈺',
            ':u6307:' => '🈯️', ':u6708:' => '🈷️', ':u6709:' => '🈶', ':u6e80:' => '🈵',
            ':u7121:' => '🈚️', ':u7533:' => '🈸', ':u7a7a:' => '🈳', ':u7981:' => '🈲',
            ':sa:' => '🈂️', ':restroom:' => '🚻', ':mens:' => '🚹', ':womens:' => '🚺',
            ':baby_symbol:' => '🚼', ':no_smoking:' => '🚭', ':parking:' => '🅿️', ':wheelchair:' => '♿️',
            ':metro:' => '🚇', ':baggage_claim:' => '🛄', ':accept:' => '🉑', ':wc:' => '🚾',
            ':potable_water:' => '🚰', ':put_litter_in_its_place:' => '🚮', ':secret:' => '㊙️', ':congratulations:' => '㊗️',
            ':m:' => 'Ⓜ️', ':passport_control:' => '🛂', ':left_luggage:' => '🛅', ':customs:' => '🛃',
            ':ideograph_advantage:' => '🉐', ':cl:' => '🆑', ':sos:' => '🆘', ':id:' => '🆔',
            ':no_entry_sign:' => '🚫', ':underage:' => '🔞', ':no_mobile_phones:' => '📵', ':do_not_litter:' => '🚯',
            ':non-potable_water:' => '🚱', ':no_bicycles:' => '🚳', ':no_pedestrians:' => '🚷', ':children_crossing:' => '🚸',
            ':no_entry:' => '⛔️', ':eight_spoked_asterisk:' => '✳️', ':eight_pointed_black_star:' => '✴️', ':heart_decoration:' => '💟',
            ':vs:' => '🆚', ':vibration_mode:' => '📳', ':mobile_phone_off:' => '📴', ':chart:' => '💹',
            ':currency_exchange:' => '💱', ':aries:' => '♈️', ':taurus:' => '♉️', ':gemini:' => '♊️',
            ':cancer:' => '♋️', ':leo:' => '♌️', ':virgo:' => '♍️', ':libra:' => '♎️',
            ':scorpius:' => '♏️', ':sagittarius:' => '♐️', ':capricorn:' => '♑️', ':aquarius:' => '♒️',
            ':pisces:' => '♓️', ':ophiuchus:' => '⛎', ':six_pointed_star:' => '🔯', ':negative_squared_cross_mark:' => '❎',
            ':a:' => '🅰️', ':b:' => '🅱️', ':ab:' => '🆎', ':o2:' => '🅾️',
            ':diamond_shape_with_a_dot_inside:' => '💠', ':recycle:' => '♻️', ':end:' => '🔚', ':on:' => '🔛',
            ':soon:' => '🔜', ':clock1:' => '🕐', ':clock130:' => '🕜', ':clock10:' => '🕙',
            ':clock1030:' => '🕥', ':clock11:' => '🕚', ':clock1130:' => '🕦', ':clock12:' => '🕛',
            ':clock1230:' => '🕧', ':clock2:' => '🕑', ':clock230:' => '🕝', ':clock3:' => '🕒',
            ':clock330:' => '🕞', ':clock4:' => '🕓', ':clock430:' => '🕟', ':clock5:' => '🕔',
            ':clock530:' => '🕠', ':clock6:' => '🕕', ':clock630:' => '🕡', ':clock7:' => '🕖',
            ':clock730:' => '🕢', ':clock8:' => '🕗', ':clock830:' => '🕣', ':clock9:' => '🕘',
            ':clock930:' => '🕤', ':heavy_dollar_sign:' => '💲', ':copyright:' => '©️', ':registered:' => '®️',
            ':tm:' => '™️', ':x:' => '❌', ':heavy_exclamation_mark:' => '❗️', ':bangbang:' => '‼️',
            ':interrobang:' => '⁉️', ':o:' => '⭕️', ':heavy_multiplication_x:' => '✖️', ':heavy_plus_sign:' => '➕',
            ':heavy_minus_sign:' => '➖', ':heavy_division_sign:' => '➗', ':white_flower:' => '💮', ':100:' => '💯',
            ':heavy_check_mark:' => '✔️', ':ballot_box_with_check:' => '☑️', ':radio_button:' => '🔘', ':link:' => '🔗',
            ':curly_loop:' => '➰', ':wavy_dash:' => '〰️', ':part_alternation_mark:' => '〽️', ':trident:' => '🔱',
            ':white_check_mark:' => '✅', ':black_square_button:' => '🔲', ':white_square_button:' => '🔳', ':black_circle:' => '⚫️',
            ':white_circle:' => '⚪️', ':red_circle:' => '🔴', ':large_blue_circle:' => '🔵', ':large_blue_diamond:' => '🔷',
            ':large_orange_diamond:' => '🔶', ':small_blue_diamond:' => '🔹', ':small_orange_diamond:' => '🔸', ':small_red_triangle:' => '🔺',
            ':small_red_triangle_down:' => '🔻', ':black_small_square:' => '▪️', ':black_medium_small_square:' => '◾', ':black_medium_square:' => '◼️',
            ':black_large_square:' => '⬛', ':white_small_square:' => '▫️', ':white_medium_small_square:' => '◽', ':white_medium_square:' => '◻️',
            ':white_large_square:' => '⬜',
        ];

        if (preg_match('/^(:)([^: ]*?)(:)/', $excerpt['text'], $matches)) {
            return [
                'extent' => strlen($matches[0]),
                'element' => [
                    'text' => str_replace(array_keys($emojiMap), $emojiMap, $matches[0]),
                ],
            ];
        }
    }

    // Inline Marks

    protected function inlineMark($excerpt)
    {
        if (preg_match('/^(==)([^=]*?)(==)/', $excerpt['text'], $matches)) {
            return [
                'extent' => strlen($matches[0]),
                'element' => [
                    'name' => 'mark',
                    'text' => $matches[2],
                ],
            ];
        }
    }

    // Inline Keystrokes

    protected function inlineKeystrokes($excerpt)
    {
        if (preg_match('/^(?<!\[)(?:\[\[([^\[\]]*|[\[\]])\]\])(?!\])/s', $excerpt['text'], $matches)) {
            return [
                'extent' => strlen($matches[0]),
                'element' => [
                    'name' => 'kbd',
                    'text' => $matches[1],
                ],
            ];
        }
    }

    // Inline Superscript

    protected function inlineSuperscript($excerpt)
    {
        if (preg_match('/(?:\^(?!\^)([^\^ ]*)\^(?!\^))/', $excerpt['text'], $matches)) {
            return [
                'extent' => strlen($matches[0]),
                'element' => [
                    'name' => 'sup',
                    'text' => $matches[1],
                    'function' => 'lineElements',
                ],
            ];
        }
    }

    // Inline Subscript

    protected function inlineSubscript($excerpt)
    {
        if (preg_match('/(?:~(?!~)([^~ ]*)~(?!~))/', $excerpt['text'], $matches)) {
            return [
                'extent' => strlen($matches[0]),
                'element' => [
                    'name' => 'sub',
                    'text' => $matches[1],
                    'function' => 'lineElements',
                ],
            ];
        }
    }

    // Inline typographer

    protected function inlineTypographer($excerpt)
    {
        $substitutions = [
            '/\(c\)/i' => '&copy;',
            '/\(r\)/i' => '&reg;',
            '/\(tm\)/i' => '&trade;',
            '/\(p\)/i' => '&para;',
            '/\+-/i' => '&plusmn;',
            '/\.{4,}|\.{2}/i' => '...',
            '/\!\.{3,}/i' => '!..',
            '/\?\.{3,}/i' => '?..',
        ];

        if (preg_match('/\+-|\(p\)|\(tm\)|\(r\)|\(c\)|\.{2,}|\!\.{3,}|\?\.{3,}/i', $excerpt['text'], $matches)) {
            return [
                'extent' => strlen($matches[0]),
                'element' => [
                    'rawHtml' => preg_replace(array_keys($substitutions), array_values($substitutions), $matches[0]),
                ],
            ];
        }
    }

    // Inline Smartypants

    protected function inlineSmartypants($excerpt)
    {
        // Substitutions
        $backtickDoublequoteOpen = $this->options['smarty']['substitutions']['left-double-quote'] ?? '&ldquo;';
        $backtickDoublequoteClose = $this->options['smarty']['substitutions']['right-double-quote'] ?? '&rdquo;';

        $smartDoublequoteOpen = $this->options['smarty']['substitutions']['left-double-quote'] ?? '&ldquo;';
        $smartDoublequoteClose = $this->options['smarty']['substitutions']['right-double-quote'] ?? '&rdquo;';
        $smartSinglequoteOpen = $this->options['smarty']['substitutions']['left-single-quote'] ?? '&lsquo;';
        $smartSinglequoteClose = $this->options['smarty']['substitutions']['right-single-quote'] ?? '&rsquo;';

        $leftAngleQuote = $this->options['smarty']['substitutions']['left-angle-quote'] ?? '&laquo;';
        $rightAngleQuote = $this->options['smarty']['substitutions']['right-angle-quote'] ?? '&raquo;';

        if (preg_match('/(``)(?!\s)([^"\'`]{1,})(\'\')|(\")(?!\s)([^\"]{1,})(\")|(\')(?!\s)([^\']{1,})(\')|(<{2})(?!\s)([^<>]{1,})(>{2})|(\.{3})|(-{3})|(-{2})/i', $excerpt['text'], $matches)) {
            $matches = array_values(array_filter($matches));

            // Smart backticks
            $smartBackticks = $this->options['smarty']['smart_backticks'] ?? false;

            if ($smartBackticks) {
                if ('``' === $matches[1]) {
                    $length = strlen(trim($excerpt['before']));
                    if ($length > 0) {
                        return;
                    }

                    return [
                        'extent' => strlen($matches[0]),
                        'element' => [
                            'text' => html_entity_decode($backtickDoublequoteOpen).$matches[2].html_entity_decode($backtickDoublequoteClose),
                        ],
                    ];
                }
            }

            // Smart quotes
            $smartQuotes = $this->options['smarty']['smart_quotes'] ?? true;

            if ($smartQuotes) {
                if ("'" === $matches[1]) {
                    $length = strlen(trim($excerpt['before']));
                    if ($length > 0) {
                        return;
                    }

                    return [
                        'extent' => strlen($matches[0]),
                        'element' => [
                            'text' => html_entity_decode($smartSinglequoteOpen).$matches[2].html_entity_decode($smartSinglequoteClose),
                        ],
                    ];
                }

                if ('"' === $matches[1]) {
                    $length = strlen(trim($excerpt['before']));
                    if ($length > 0) {
                        return;
                    }

                    return [
                        'extent' => strlen($matches[0]),
                        'element' => [
                            'text' => html_entity_decode($smartDoublequoteOpen).$matches[2].html_entity_decode($smartDoublequoteClose),
                        ],
                    ];
                }
            }

            // Smart angled quotes
            $smartAngledQuotes = $this->options['smarty']['smart_angled_quotes'] ?? true;

            if ($smartAngledQuotes) {
                if ('<<' === $matches[1]) {
                    $length = strlen(trim($excerpt['before']));
                    if ($length > 0) {
                        return;
                    }

                    return [
                        'extent' => strlen($matches[0]),
                        'element' => [
                            'text' => html_entity_decode($leftAngleQuote).$matches[2].html_entity_decode($rightAngleQuote),
                        ],
                    ];
                }
            }

            // Smart dashes
            $smartDashes = $this->options['smarty']['smart_dashes'] ?? true;

            if ($smartDashes) {
                if ('---' === $matches[1]) {
                    return [
                        'extent' => strlen($matches[0]),
                        'element' => [
                            'rawHtml' => $this->options['smarty']['substitutions']['mdash'] ?? '&mdash;',
                        ],
                    ];
                }

                if ('--' === $matches[1]) {
                    return [
                        'extent' => strlen($matches[0]),
                        'element' => [
                            'rawHtml' => $this->options['smarty']['substitutions']['ndash'] ?? '&ndash;',
                        ],
                    ];
                }
            }

            // Smart ellipses
            $smartEllipses = $this->options['smarty']['smart_ellipses'] ?? true;

            if ($smartEllipses) {
                if ('...' === $matches[1]) {
                    return [
                        'extent' => strlen($matches[0]),
                        'element' => [
                            'rawHtml' => $this->options['smarty']['substitutions']['ellipses'] ?? '&hellip;',
                        ],
                    ];
                }
            }
        }
    }

    // Inline Math

    protected function inlineMath($excerpt)
    {
        $matchSingleDollar = $this->options['math']['single_dollar'] ?? false;
        // Inline Matches
        if ($matchSingleDollar) {
            // Match single dollar - experimental
            if (preg_match('/^(?<!\\\\)((?<!\$)\$(?!\$)(.*?)(?<!\$)\$(?!\$)|(?<!\\\\\()\\\\\((.*?)(?<!\\\\\()\\\\\)(?!\\\\\)))/s', $excerpt['text'], $matches)) {
                $mathMatch = $matches[0];
            }
        } else {
            if (preg_match('/^(?<!\\\\\()\\\\\((.*?)(?<!\\\\\()\\\\\)(?!\\\\\))/s', $excerpt['text'], $matches)) {
                $mathMatch = $matches[0];
            }
        }

        if (isset($mathMatch)) {
            return [
                'extent' => strlen($mathMatch),
                'element' => [
                    'text' => $mathMatch,
                ],
            ];
        }
    }

    protected function inlineEscapeSequence($excerpt)
    {
        $element = [
            'element' => [
                'rawHtml' => $excerpt['text'][1],
            ],
            'extent' => 2,
        ];

        $state = $this->options['math'] ?? false;

        if ($state) {
            if (isset($excerpt['text'][1]) && in_array($excerpt['text'][1], $this->specialCharacters) && !preg_match('/^(?<!\\\\)(?<!\\\\\()\\\\\((.{2,}?)(?<!\\\\\()\\\\\)(?!\\\\\))/s', $excerpt['text'])) {
                return $element;
            }
        } else {
            if (isset($excerpt['text'][1]) && in_array($excerpt['text'][1], $this->specialCharacters)) {
                return $element;
            }
        }
    }

    /**
     * ------------------------------------------------------------------------
     *  Blocks.
     * ------------------------------------------------------------------------
     */
    protected function blockFootnote($line, array $_ = null)
    {
        $state = $this->options['footnotes'] ?? true;
        if ($state) {
            return $this->blockFootnoteBase($line);
        }
    }

    protected function blockDefinitionList($line, $block)
    {
        $state = $this->options['definition_lists'] ?? true;
        if ($state) {
            return $this->blockDefinitionListBase($line, $block);
        }
    }

    protected function blockCode($line, $block = null)
    {
        $codeBlock = $this->options['code']['blocks'] ?? true;
        $codeMain = $this->options['code'] ?? true;
        if ($codeBlock === true and $codeMain === true) {
            return $this->blockCodeBase($line, $block);
        }
    }

    protected function blockComment($line, array $_ = null)
    {
        $state = $this->options['comments'] ?? true;
        if ($state) {
            return $this->blockCommentBase($line);
        }
    }

    protected function blockHeader($line, array $_ = null)
    {
        $state = $this->options['headings'] ?? true;
        if (!$state) {
            return;
        }

        $block = $this->blockHeaderBase($line);
        if (!empty($block)) {
            // Get the text of the heading
            if (isset($block['element']['handler']['argument'])) {
                $text = $block['element']['handler']['argument'];
            }

            // Get the heading level. Levels are h1, h2, ..., h6
            $level = $block['element']['name'];

            $headersAllowed = $this->options['headings']['allowed'] ?? ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
            if (!in_array($level, $headersAllowed)) {
                return;
            }

            // Checks if auto generated anchors is allowed
            $autoAnchors = $this->options['headings']['auto_anchors'] ?? true;

            if ($autoAnchors) {
                // Get the anchor of the heading to link from the ToC list
                $id = $block['element']['attributes']['id'] ?? $this->createAnchorID($text);
            } else {
                // Get the anchor of the heading to link from the ToC list
                $id = $block['element']['attributes']['id'] ?? null;
            }

            // Set attributes to head tags
            $block['element']['attributes']['id'] = $id;

            $tocHeaders = $this->options['toc']['headings'] ?? ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
            // Check if level are defined as a heading
            if (in_array($level, $tocHeaders)) {
                // Add/stores the heading element info to the ToC list
                $this->setContentsList([
                    'text' => $text,
                    'id' => $id,
                    'level' => $level,
                ]);
            }

            return $block;
        }
    }

    protected function blockList($line, array $CurrentBlock = null)
    {
        $state = $this->options['lists'] ?? true;
        if ($state) {
            return $this->blockListBase($line, $CurrentBlock);
        }
    }

    protected function blockQuote($line, array $_ = null)
    {
        $state = $this->options['qoutes'] ?? true;
        if ($state) {
            return $this->blockQuoteBase($line);
        }
    }

    protected function blockRule($line, array $_ = null)
    {
        $state = $this->options['thematic_breaks'] ?? true;
        if ($state) {
            return $this->blockRuleBase($line);
        }
    }

    protected function blockSetextHeader($line, $block = null)
    {
        $state = $this->options['headings'] ?? true;
        if (!$state) {
            return;
        }
        $block = $this->blockSetextHeaderBase($line, $block);
        if (!empty($block)) {
            // Get the text of the heading
            if (isset($block['element']['handler']['argument'])) {
                $text = $block['element']['handler']['argument'];
            }

            // Get the heading level. Levels are h1, h2, ..., h6
            $level = $block['element']['name'];

            $headersAllowed = $this->options['headings']['allowed'] ?? ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
            if (!in_array($level, $headersAllowed)) {
                return;
            }

            // Checks if auto generated anchors is allowed
            $autoAnchors = $this->options['headings']['auto_anchors'] ?? true;

            if ($autoAnchors) {
                // Get the anchor of the heading to link from the ToC list
                $id = $block['element']['attributes']['id'] ?? $this->createAnchorID($text);
            } else {
                // Get the anchor of the heading to link from the ToC list
                $id = $block['element']['attributes']['id'] ?? null;
            }

            // Set attributes to head tags
            $block['element']['attributes']['id'] = $id;

            $headersAllowed = $this->options['headings']['allowed'] ?? ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];

            // Check if level are defined as a heading
            if (in_array($level, $headersAllowed)) {
                // Add/stores the heading element info to the ToC list
                $this->setContentsList([
                    'text' => $text,
                    'id' => $id,
                    'level' => $level,
                ]);
            }

            return $block;
        }
    }

    protected function blockMarkup($line, array $_ = null)
    {
        $state = $this->options['markup'] ?? true;
        if ($state) {
            return $this->blockMarkupBase($line);
        }
    }

    protected function blockReference($line, array $_ = null)
    {
        $state = $this->options['references'] ?? true;
        if ($state) {
            return $this->blockReferenceBase($line);
        }
    }

    protected function blockTable($line, $block = null)
    {
        $state = $this->options['tables'] ?? true;
        if ($state) {
            return $this->blockTableBase($line, $block);
        }
    }

    protected function blockAbbreviation($line, array $_ = null)
    {
        $allowCustomAbbr = $this->options['abbreviations']['allow_custom_abbr'] ?? true;

        $state = $this->options['abbreviations'] ?? true;
        if ($state) {
            if (isset($this->options['abbreviations']['predefine'])) {
                foreach ($this->options['abbreviations']['predefine'] as $abbreviations => $description) {
                    $this->DefinitionData['Abbreviation'][$abbreviations] = $description;
                }
            }

            if ($allowCustomAbbr == true) {
                return $this->blockAbbreviationBase($line);
            }

            return;
        }
    }

    // Block Math

    protected function blockMath($line, array $_ = null)
    {
        $block = [
            'element' => [
                'text' => '',
            ],
        ];

        if (preg_match('/^(?<!\\\\)(\\\\\[)(?!.)$/', $line['text'])) {
            $block['end'] = '\]';

            return $block;
        }
        if (preg_match('/^(?<!\\\\)(\$\$)(?!.)$/', $line['text'])) {
            $block['end'] = '$$';

            return $block;
        }
    }

    // ~

    protected function blockMathContinue($line, $block)
    {
        if (isset($block['complete'])) {
            return;
        }

        if (isset($block['interrupted'])) {
            $block['element']['text'] .= str_repeat(
                "\n",
                $block['interrupted']
            );

            unset($block['interrupted']);
        }

        if (preg_match('/^(?<!\\\\)(\\\\\])$/', $line['text']) && '\]' === $block['end']) {
            $block['complete'] = true;
            $block['math'] = true;
            $block['element']['text'] =
             '\\['.$block['element']['text'].'\\]';

            return $block;
        }
        if (preg_match('/^(?<!\\\\)(\$\$)$/', $line['text']) && '$$' === $block['end']) {
            $block['complete'] = true;
            $block['math'] = true;
            $block['element']['text'] = '$$'.$block['element']['text'].'$$';

            return $block;
        }

        $block['element']['text'] .= "\n".$line['body'];

        // ~

        return $block;
    }

    // ~

    protected function blockMathComplete($block)
    {
        return $block;
    }

    // Block Fenced Code

    protected function blockFencedCode($line, array $_ = null)
    {
        $codeBlock = $this->options['code']['blocks'] ?? true;
        $codeMain = $this->options['code'] ?? true;
        if ($codeBlock === false or $codeMain === false) {
            return;
        }
        $block = $this->blockFencedCodeBase($line);

        $marker = $line['text'][0];
        $openerLength = strspn($line['text'], $marker);
        $language = trim(
            preg_replace('/^`{3}([^\s]+)(.+)?/s', '$1', $line['text'])
        );

        $state = $this->options['diagrams'] ?? true;
        if ($state) {
            // Mermaid.js https://mermaidjs.github.io
            if ('mermaid' == strtolower($language)) {
                $element = [
                    'text' => '',
                ];

                return [
                    'char' => $marker,
                    'openerLength' => $openerLength,
                    'element' => [
                        'element' => $element,
                        'name' => 'div',
                        'attributes' => [
                            'class' => 'mermaid',
                        ],
                    ],
                ];
            }

            // Chart.js https://www.chartjs.org/
            if ('chart' == strtolower($language)) {
                $element = [
                    'text' => '',
                ];

                return [
                    'char' => $marker,
                    'openerLength' => $openerLength,
                    'element' => [
                        'element' => $element,
                        'name' => 'canvas',
                        'attributes' => [
                            'class' => 'chartjs',
                        ],
                    ],
                ];
            }
        }

        return $block;
    }

    // Parsedown Tablespan from @KENNYSOFT
    protected function blockTableComplete(array $block)
    {
        $state = $this->options['tables']['tablespan'] ?? false;
        if ($state === false) {
            return $block;
        }

        if (!isset($block)) {
            return null;
        }

        $HeaderElements = &$block['element']['elements'][0]['elements'][0]['elements'];

        for ($index = count($HeaderElements) - 1; $index >= 0; --$index) {
            $colspan = 1;
            $HeaderElement = &$HeaderElements[$index];

            while ($index && '>' === $HeaderElements[$index - 1]['handler']['argument']) {
                ++$colspan;
                $PreviousHeaderElement = &$HeaderElements[--$index];
                $PreviousHeaderElement['merged'] = true;
                if (isset($PreviousHeaderElement['attributes'])) {
                    $HeaderElement['attributes'] = $PreviousHeaderElement['attributes'];
                }
            }

            if ($colspan > 1) {
                if (!isset($HeaderElement['attributes'])) {
                    $HeaderElement['attributes'] = [];
                }
                $HeaderElement['attributes']['colspan'] = $colspan;
            }
        }

        for ($index = count($HeaderElements) - 1; $index >= 0; --$index) {
            if (isset($HeaderElements[$index]['merged'])) {
                array_splice($HeaderElements, $index, 1);
            }
        }

        $rows = &$block['element']['elements'][1]['elements'];

        foreach ($rows as $rowNo => &$row) {
            $elements = &$row['elements'];

            for ($index = count($elements) - 1; $index >= 0; --$index) {
                $colspan = 1;
                $element = &$elements[$index];

                while ($index && '>' === $elements[$index - 1]['handler']['argument']) {
                    ++$colspan;
                    $PreviousElement = &$elements[--$index];
                    $PreviousElement['merged'] = true;
                    if (isset($PreviousElement['attributes'])) {
                        $element['attributes'] = $PreviousElement['attributes'];
                    }
                }

                if ($colspan > 1) {
                    if (!isset($element['attributes'])) {
                        $element['attributes'] = [];
                    }
                    $element['attributes']['colspan'] = $colspan;
                }
            }
        }

        foreach ($rows as $rowNo => &$row) {
            $elements = &$row['elements'];

            foreach ($elements as $index => &$element) {
                $rowspan = 1;

                if (isset($element['merged'])) {
                    continue;
                }

                while ($rowNo + $rowspan < count($rows) && $index < count($rows[$rowNo + $rowspan]['elements']) && '^' === $rows[$rowNo + $rowspan]['elements'][$index]['handler']['argument'] && (@$element['attributes']['colspan'] ?: null) === (@$rows[$rowNo + $rowspan]['elements'][$index]['attributes']['colspan'] ?: null)) {
                    $rows[$rowNo + $rowspan]['elements'][$index]['merged'] = true;
                    ++$rowspan;
                }

                if ($rowspan > 1) {
                    if (!isset($element['attributes'])) {
                        $element['attributes'] = [];
                    }
                    $element['attributes']['rowspan'] = $rowspan;
                }
            }
        }

        foreach ($rows as $rowNo => &$row) {
            $elements = &$row['elements'];

            for ($index = count($elements) - 1; $index >= 0; --$index) {
                if (isset($elements[$index]['merged'])) {
                    array_splice($elements, $index, 1);
                }
            }
        }

        return $block;
    }

    /*
    * Checkbox
    * -------------------------------------------------------------------------
    */
    protected function blockCheckbox($line)
    {
        $text = trim($line['text']);
        $beginLine = substr($text, 0, 4);
        if ('[ ] ' === $beginLine) {
            return [
                'handler' => 'checkboxUnchecked',
                'text' => substr(trim($text), 4),
            ];
        }

        if ('[x] ' === $beginLine) {
            return [
                'handler' => 'checkboxChecked',
                'text' => substr(trim($text), 4),
            ];
        }
    }

    protected function blockCheckboxContinue(array $block)
    {
        // This is here because Parsedown require it.
    }

    protected function blockCheckboxComplete(array $block)
    {
        $block['element'] = [
            'rawHtml' => $this->{$block['handler']}($block['text']),
            'allowRawHtmlInSafeMode' => true,
        ];

        return $block;
    }

    protected function checkboxUnchecked($text)
    {
        if ($this->markupEscaped || $this->safeMode) {
            $text = self::escape($text);
        }

        return '<input type="checkbox" disabled /> '.$this->format($text);
    }

    protected function checkboxChecked($text)
    {
        if ($this->markupEscaped || $this->safeMode) {
            $text = self::escape($text);
        }

        return '<input type="checkbox" checked disabled /> '.$this->format($text);
    }

    /**
     * ------------------------------------------------------------------------
     *  Helpers.
     * ------------------------------------------------------------------------.
     */

    /**
     * Formats the checkbox label without double escaping.
     */
    protected function format($text)
    {
        // backup settings
        $markupEscaped = $this->markupEscaped;
        $safeMode = $this->safeMode;

        // disable rules to prevent double escaping.
        $this->setMarkupEscaped(false);
        $this->setSafeMode(false);

        // format line
        $text = $this->line($text);

        // reset old values
        $this->setMarkupEscaped($markupEscaped);
        $this->setSafeMode($safeMode);

        return $text;
    }

    protected function parseAttributeData($attributeString)
    {
        $state = $this->options['special_attributes'] ?? true;
        if ($state) {
            return $this->parseAttributeDataBase($attributeString);
        }

        return [];
    }

    /**
     * Encodes the ToC tag to a hashed tag and replace.
     *
     * This is used to avoid parsing user defined ToC tag which includes "_" in
     * their tag such as "[[_toc_]]". Unless it will be parsed as:
     *   "<p>[[<em>TOC</em>]]</p>"
     */
    protected function encodeTagToHash($text)
    {
        $salt = $this->getSalt();
        $tagOrigin = $this->getTagToC();

        if (strpos($text, $tagOrigin) === false) {
            return $text;
        }

        $tagHashed = hash('sha256', $salt.$tagOrigin);

        return str_replace($tagOrigin, $tagHashed, $text);
    }

    /**
     * Decodes the hashed ToC tag to an original tag and replaces.
     *
     * This is used to avoid parsing user defined ToC tag which includes "_" in
     * their tag such as "[[_toc_]]". Unless it will be parsed as:
     *   "<p>[[<em>TOC</em>]]</p>"
     */
    protected function decodeTagFromHash($text)
    {
        $salt = $this->getSalt();
        $tagOrigin = $this->getTagToC();
        $tagHashed = hash('sha256', $salt.$tagOrigin);

        if (strpos($text, $tagHashed) === false) {
            return $text;
        }

        return str_replace($tagHashed, $tagOrigin, $text);
    }

    /**
     * Unique string to use as a salt value.
     */
    protected function getSalt()
    {
        static $salt;
        if (isset($salt)) {
            return $salt;
        }

        $salt = hash('md5', (string) \time());

        return $salt;
    }

    /**
     * Gets the markdown tag for ToC.
     */
    protected function getTagToC()
    {
        return $this->options['toc']['set_toc_tag'] ?? '[toc]';
    }

    /**
     * Gets the ID attribute of the ToC for HTML tags.
     */
    protected function getIdAttributeToC()
    {
        if (!empty($this->idToc)) {
            return $this->idToc;
        }

        return self::ID_ATTRIBUTE_DEFAULT;
    }

    /**
     * Generates an anchor text that are link-able even if the heading is not in
     * ASCII.
     */
    protected function createAnchorID($str): string
    {
        $optionUrlEncode = $this->options['toc']['urlencode'] ?? false;
        if ($optionUrlEncode) {
            // Check AnchorID is unique
            $str = $this->incrementAnchorId($str);

            return urlencode($str);
        }

        $charMap = [
            // Latin
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'AA', 'Æ' => 'AE', 'Ç' => 'C',
            'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
            'Ð' => 'D', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ő' => 'O',
            'Ø' => 'OE', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ű' => 'U', 'Ý' => 'Y', 'Þ' => 'TH',
            'ß' => 'ss',
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'aa', 'æ' => 'ae', 'ç' => 'c',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'ð' => 'd', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ő' => 'o',
            'ø' => 'oe', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ű' => 'u', 'ý' => 'y', 'þ' => 'th',
            'ÿ' => 'y',

            // Latin symbols
            '©' => '(c)', '®' => '(r)', '™' => '(tm)',

            // Greek
            'Α' => 'A', 'Β' => 'B', 'Γ' => 'G', 'Δ' => 'D', 'Ε' => 'E', 'Ζ' => 'Z', 'Η' => 'H', 'Θ' => '8',
            'Ι' => 'I', 'Κ' => 'K', 'Λ' => 'L', 'Μ' => 'M', 'Ν' => 'N', 'Ξ' => '3', 'Ο' => 'O', 'Π' => 'P',
            'Ρ' => 'R', 'Σ' => 'S', 'Τ' => 'T', 'Υ' => 'Y', 'Φ' => 'F', 'Χ' => 'X', 'Ψ' => 'PS', 'Ω' => 'W',
            'Ά' => 'A', 'Έ' => 'E', 'Ί' => 'I', 'Ό' => 'O', 'Ύ' => 'Y', 'Ή' => 'H', 'Ώ' => 'W', 'Ϊ' => 'I',
            'Ϋ' => 'Y',
            'α' => 'a', 'β' => 'b', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e', 'ζ' => 'z', 'η' => 'h', 'θ' => '8',
            'ι' => 'i', 'κ' => 'k', 'λ' => 'l', 'μ' => 'm', 'ν' => 'n', 'ξ' => '3', 'ο' => 'o', 'π' => 'p',
            'ρ' => 'r', 'σ' => 's', 'τ' => 't', 'υ' => 'y', 'φ' => 'f', 'χ' => 'x', 'ψ' => 'ps', 'ω' => 'w',
            'ά' => 'a', 'έ' => 'e', 'ί' => 'i', 'ό' => 'o', 'ύ' => 'y', 'ή' => 'h', 'ώ' => 'w', 'ς' => 's',
            'ϊ' => 'i', 'ΰ' => 'y', 'ϋ' => 'y', 'ΐ' => 'i',

            // Turkish
            'Ş' => 'S', 'İ' => 'I', 'Ğ' => 'G',
            'ş' => 's', 'ı' => 'i', 'ğ' => 'g',

            // Russian
            'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'Yo', 'Ж' => 'Zh',
            'З' => 'Z', 'И' => 'I', 'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
            'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
            'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sh', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'Yu',
            'Я' => 'Ya',
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh',
            'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
            'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
            'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu',
            'я' => 'ya',

            // Ukrainian
            'Є' => 'Ye', 'І' => 'I', 'Ї' => 'Yi', 'Ґ' => 'G',
            'є' => 'ye', 'і' => 'i', 'ї' => 'yi', 'ґ' => 'g',

            // Czech
            'Č' => 'C', 'Ď' => 'D', 'Ě' => 'E', 'Ň' => 'N', 'Ř' => 'R', 'Š' => 'S', 'Ť' => 'T', 'Ů' => 'U',
            'Ž' => 'Z',
            'č' => 'c', 'ď' => 'd', 'ě' => 'e', 'ň' => 'n', 'ř' => 'r', 'š' => 's', 'ť' => 't', 'ů' => 'u',
            'ž' => 'z',

            // Polish
            'Ą' => 'A', 'Ć' => 'C', 'Ę' => 'e', 'Ł' => 'L', 'Ń' => 'N', 'Ś' => 'S', 'Ź' => 'Z',
            'Ż' => 'Z',
            'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n', 'ś' => 's', 'ź' => 'z',
            'ż' => 'z',

            // Latvian
            'Ā' => 'A', 'Ē' => 'E', 'Ģ' => 'G', 'Ī' => 'i', 'Ķ' => 'k', 'Ļ' => 'L', 'Ņ' => 'N', 'Ū' => 'u',
            'ā' => 'a', 'ē' => 'e', 'ģ' => 'g', 'ī' => 'i', 'ķ' => 'k', 'ļ' => 'l', 'ņ' => 'n', 'ū' => 'u',
        ];

        // Transliterate characters to ASCII
        $optionTransliterate = $this->options['toc']['transliterate'] ?? false;
        if ($optionTransliterate) {
            $str = str_replace(array_keys($charMap), $charMap, $str);
        }

        // Replace non-alphanumeric characters with our delimiter
        $optionDelimiter = $this->options['toc']['delimiter'] ?? '-';
        $str = preg_replace('/[^\p{L}\p{Nd}]+/u', $optionDelimiter, $str);

        // Remove duplicate delimiters
        $str = preg_replace('/('.preg_quote($optionDelimiter, '/').'){2,}/', '$1', $str);

        // Truncate slug to max. characters
        $optionLimit = $this->options['toc']['limit'] ?? mb_strlen($str, 'UTF-8');
        $str = mb_substr($str, 0, $optionLimit, 'UTF-8');

        // Remove delimiter from ends
        $str = trim($str, $optionDelimiter);

        $urlLowercase = $this->options['toc']['lowercase'] ?? true;
        $str = $urlLowercase ? mb_strtolower($str, 'UTF-8') : $str;

        return $this->incrementAnchorId($str);
    }

    /**
     * Get only the text from a markdown string.
     * It parses to HTML once then trims the tags to get the text.
     */
    protected function fetchText($text)
    {
        return trim(strip_tags($this->line($text)));
    }

    /**
     * Set/stores the heading block to ToC list in a string and array format.
     */
    protected function setContentsList(array $Content)
    {
        // Stores as an array
        $this->setContentsListAsArray($Content);
        // Stores as string in markdown list format.
        $this->setContentsListAsString($Content);
    }

    /**
     * Sets/stores the heading block info as an array.
     */
    protected function setContentsListAsArray(array $Content)
    {
        $this->contentsListArray[] = $Content;
    }

    /**
     * Sets/stores the heading block info as a list in markdown format.
     */
    protected function setContentsListAsString(array $Content)
    {
        $text = $this->fetchText($Content['text']);
        $id = $Content['id'];
        $level = (int) trim($Content['level'], 'h');
        $link = "[{$text}](#{$id})";

        if (0 === $this->firstHeadLevel) {
            $this->firstHeadLevel = $level;
        }
        $cutIndent = $this->firstHeadLevel - 1;
        if ($cutIndent > $level) {
            $level = 1;
        } else {
            $level = $level - $cutIndent;
        }

        $indent = str_repeat('  ', $level);

        // Stores in markdown list format as below:
        // - [Header1](#Header1)
        //   - [Header2-1](#Header2-1)
        //     - [Header3](#Header3)
        //   - [Header2-2](#Header2-2)
        // ...
        $this->contentsListString .= "{$indent}- {$link}".PHP_EOL;
    }

    /**
     * Collect and count anchors in use to prevent duplicated ids. Return string
     * with incremental, numeric suffix. Also init optional blacklist of ids.
     */
    protected function incrementAnchorId($str)
    {
        // add blacklist to list of used anchors
        if (!$this->isBlacklistInitialized) {
            $this->initBlacklist();
        }

        $this->anchorDuplicates[$str] = !isset($this->anchorDuplicates[$str]) ? 0 : ++$this->anchorDuplicates[$str];

        $newStr = $str;

        if ($count = $this->anchorDuplicates[$str]) {
            $newStr .= "-{$count}";

            // increment until conversion doesn't produce new duplicates anymore
            if (isset($this->anchorDuplicates[$newStr])) {
                $newStr = $this->incrementAnchorId($str);
            } else {
                $this->anchorDuplicates[$newStr] = 0;
            }
        }

        return $newStr;
    }

    /**
     * Add blacklisted ids to anchor list.
     */
    protected function initBlacklist()
    {
        if ($this->isBlacklistInitialized) {
            return;
        }

        if (!empty($this->options['headings']['blacklist']) && is_array($this->options['headings']['blacklist'])) {
            foreach ($this->options['headings']['blacklist'] as $v) {
                if (is_string($v)) {
                    $this->anchorDuplicates[$v] = 0;
                }
            }
        }

        $this->isBlacklistInitialized = true;
    }

    protected function lineElements($text, $nonNestables = [])
    {
        $Elements = [];

        $nonNestables = (
            empty($nonNestables)
            ? []
            : array_combine($nonNestables, $nonNestables)
        );

        // $excerpt is based on the first occurrence of a marker

        while ($excerpt = strpbrk($text, $this->inlineMarkerList)) {
            $marker = $excerpt[0];

            $markerPosition = strlen($text) - strlen($excerpt);

            // Get the first char before the marker
            $beforeMarkerPosition = $markerPosition - 1;
            if ($beforeMarkerPosition >= 0) {
                $charBeforeMarker = $text[$markerPosition - 1];
            } else {
                $charBeforeMarker = '';
            }

            $Excerpt = ['text' => $excerpt, 'context' => $text, 'before' => $charBeforeMarker];

            foreach ($this->InlineTypes[$marker] as $inlineType) {
                // check to see if the current inline type is nestable in the current context

                if (isset($nonNestables[$inlineType])) {
                    continue;
                }

                $Inline = $this->{"inline{$inlineType}"}($Excerpt);

                if (!isset($Inline)) {
                    continue;
                }

                // makes sure that the inline belongs to "our" marker

                if (isset($Inline['position']) and $Inline['position'] > $markerPosition) {
                    continue;
                }

                // sets a default inline position

                if (!isset($Inline['position'])) {
                    $Inline['position'] = $markerPosition;
                }

                // cause the new element to 'inherit' our non nestables

                $Inline['element']['nonNestables'] = isset($Inline['element']['nonNestables'])
                    ? array_merge($Inline['element']['nonNestables'], $nonNestables)
                    : $nonNestables
                ;

                // the text that comes before the inline
                $unmarkedText = substr($text, 0, $Inline['position']);

                // compile the unmarked text
                $InlineText = $this->inlineText($unmarkedText);
                $Elements[] = $InlineText['element'];

                // compile the inline
                $Elements[] = $this->extractElement($Inline);

                // remove the examined text
                $text = substr($text, $Inline['position'] + $Inline['extent']);

                continue 2;
            }

            // the marker does not belong to an inline

            $unmarkedText = substr($text, 0, $markerPosition + 1);

            $InlineText = $this->inlineText($unmarkedText);
            $Elements[] = $InlineText['element'];

            $text = substr($text, $markerPosition + 1);
        }

        $InlineText = $this->inlineText($text);
        $Elements[] = $InlineText['element'];

        foreach ($Elements as &$Element) {
            if (!isset($Element['autobreak'])) {
                $Element['autobreak'] = false;
            }
        }

        return $Elements;
    }

    private function pregReplaceAssoc(array $replace, $subject)
    {
        return preg_replace(array_keys($replace), array_values($replace), $subject);
    }

    #
    # Blocks
    #

    #
    # Abbreviation

    protected function blockAbbreviationBase($Line)
    {
        if (preg_match('/^\*\[(.+?)\]:[ ]*(.+?)[ ]*$/', $Line['text'], $matches))
        {
            $this->DefinitionData['Abbreviation'][$matches[1]] = $matches[2];

            $Block = array(
                'hidden' => true,
            );

            return $Block;
        }
    }

    #
    # Footnote

    protected function blockFootnoteBase($Line)
    {
        if (preg_match('/^\[\^(.+?)\]:[ ]?(.*)$/', $Line['text'], $matches))
        {
            $Block = array(
                'label' => $matches[1],
                'text' => $matches[2],
                'hidden' => true,
            );

            return $Block;
        }
    }

    protected function blockFootnoteContinue($Line, $Block)
    {
        if ($Line['text'][0] === '[' and preg_match('/^\[\^(.+?)\]:/', $Line['text']))
        {
            return;
        }

        if (isset($Block['interrupted']))
        {
            if ($Line['indent'] >= 4)
            {
                $Block['text'] .= "\n\n" . $Line['text'];

                return $Block;
            }
        }
        else
        {
            $Block['text'] .= "\n" . $Line['text'];

            return $Block;
        }
    }

    protected function blockFootnoteComplete($Block)
    {
        $this->DefinitionData['Footnote'][$Block['label']] = array(
            'text' => $Block['text'],
            'count' => null,
            'number' => null,
        );

        return $Block;
    }

    #
    # Definition List

    protected function blockDefinitionListBase($Line, $Block)
    {
        if ( ! isset($Block) or $Block['type'] !== 'Paragraph')
        {
            return;
        }

        $Element = array(
            'name' => 'dl',
            'elements' => array(),
        );

        $terms = explode("\n", $Block['element']['handler']['argument']);

        foreach ($terms as $term)
        {
            $Element['elements'] []= array(
                'name' => 'dt',
                'handler' => array(
                    'function' => 'lineElements',
                    'argument' => $term,
                    'destination' => 'elements'
                ),
            );
        }

        $Block['element'] = $Element;

        $Block = $this->addDdElement($Line, $Block);

        return $Block;
    }

    protected function blockDefinitionListContinue($Line, array $Block)
    {
        if ($Line['text'][0] === ':')
        {
            $Block = $this->addDdElement($Line, $Block);

            return $Block;
        }
        else
        {
            if (isset($Block['interrupted']) and $Line['indent'] === 0)
            {
                return;
            }

            if (isset($Block['interrupted']))
            {
                $Block['dd']['handler']['function'] = 'textElements';
                $Block['dd']['handler']['argument'] .= "\n\n";

                $Block['dd']['handler']['destination'] = 'elements';

                unset($Block['interrupted']);
            }

            $text = substr($Line['body'], min($Line['indent'], 4));

            $Block['dd']['handler']['argument'] .= "\n" . $text;

            return $Block;
        }
    }

    #
    # Header

    protected function blockHeaderBase($Line)
    {
        $Block = $this->blockHeaderParent($Line);

        if ($Block !== null && preg_match('/[ #]*{('.$this->regexAttribute.'+)}[ ]*$/', $Block['element']['handler']['argument'], $matches, PREG_OFFSET_CAPTURE))
        {
            $attributeString = $matches[1][0];

            $Block['element']['attributes'] = $this->parseAttributeData($attributeString);

            $Block['element']['handler']['argument'] = substr($Block['element']['handler']['argument'], 0, $matches[0][1]);
        }

        return $Block;
    }

    #
    # Markup

    protected function blockMarkupBase($Line)
    {
        if ($this->markupEscaped or $this->safeMode)
        {
            return;
        }

        if (preg_match('/^<(\w[\w-]*)(?:[ ]*'.$this->regexHtmlAttribute.')*[ ]*(\/)?>/', $Line['text'], $matches))
        {
            $element = strtolower($matches[1]);

            if (in_array($element, $this->textLevelElements))
            {
                return;
            }

            $Block = array(
                'name' => $matches[1],
                'depth' => 0,
                'element' => array(
                    'rawHtml' => $Line['text'],
                    'autobreak' => true,
                ),
            );

            $length = strlen($matches[0]);
            $remainder = substr($Line['text'], $length);

            if (trim($remainder) === '')
            {
                if (isset($matches[2]) or in_array($matches[1], $this->voidElements))
                {
                    $Block['closed'] = true;
                    $Block['void'] = true;
                }
            }
            else
            {
                if (isset($matches[2]) or in_array($matches[1], $this->voidElements))
                {
                    return;
                }
                if (preg_match('/<\/'.$matches[1].'>[ ]*$/i', $remainder))
                {
                    $Block['closed'] = true;
                }
            }

            return $Block;
        }
    }

    protected function blockMarkupContinue($Line, array $Block)
    {
        if (isset($Block['closed']))
        {
            return;
        }

        if (preg_match('/^<'.$Block['name'].'(?:[ ]*'.$this->regexHtmlAttribute.')*[ ]*>/i', $Line['text'])) # open
        {
            $Block['depth'] ++;
        }

        if (preg_match('/(.*?)<\/'.$Block['name'].'>[ ]*$/i', $Line['text'], $matches)) # close
        {
            if ($Block['depth'] > 0)
            {
                $Block['depth'] --;
            }
            else
            {
                $Block['closed'] = true;
            }
        }

        if (isset($Block['interrupted']))
        {
            $Block['element']['rawHtml'] .= "\n";
            unset($Block['interrupted']);
        }

        $Block['element']['rawHtml'] .= "\n".$Line['body'];

        return $Block;
    }

    protected function blockMarkupComplete($Block)
    {
        if ( ! isset($Block['void']))
        {
            $Block['element']['rawHtml'] = $this->processTag($Block['element']['rawHtml']);
        }

        return $Block;
    }

    #
    # Setext

    protected function blockSetextHeaderBase($Line, array $Block = null)
    {
        $Block = $this->blockSetextHeaderParent($Line, $Block);

        if ($Block !== null && preg_match('/[ ]*{('.$this->regexAttribute.'+)}[ ]*$/', $Block['element']['handler']['argument'], $matches, PREG_OFFSET_CAPTURE))
        {
            $attributeString = $matches[1][0];

            $Block['element']['attributes'] = $this->parseAttributeData($attributeString);

            $Block['element']['handler']['argument'] = substr($Block['element']['handler']['argument'], 0, $matches[0][1]);
        }

        return $Block;
    }

    #
    # Inline Elements
    #

    #
    # Footnote Marker

    protected function inlineFootnoteMarker($Excerpt)
    {
        if (preg_match('/^\[\^(.+?)\]/', $Excerpt['text'], $matches))
        {
            $name = $matches[1];

            if ( ! isset($this->DefinitionData['Footnote'][$name]))
            {
                return;
            }

            $this->DefinitionData['Footnote'][$name]['count'] ++;

            if ( ! isset($this->DefinitionData['Footnote'][$name]['number']))
            {
                $this->DefinitionData['Footnote'][$name]['number'] = ++ $this->footnoteCount; # » &
            }

            $Element = array(
                'name' => 'sup',
                'attributes' => array('id' => 'fnref'.$this->DefinitionData['Footnote'][$name]['count'].':'.$name),
                'element' => array(
                    'name' => 'a',
                    'attributes' => array('href' => '#fn:'.$name, 'class' => 'footnote-ref'),
                    'text' => $this->DefinitionData['Footnote'][$name]['number'],
                ),
            );

            return array(
                'extent' => strlen($matches[0]),
                'element' => $Element,
            );
        }
    }

    private $footnoteCount = 0;

    #
    # Link

    protected function inlineLinkBase($Excerpt)
    {
        $Link = $this->inlineLinkParent($Excerpt);

        $remainder = $Link !== null ? substr($Excerpt['text'], $Link['extent']) : '';

        if (preg_match('/^[ ]*{('.$this->regexAttribute.'+)}/', $remainder, $matches))
        {
            $Link['element']['attributes'] += $this->parseAttributeData($matches[1]);

            $Link['extent'] += strlen($matches[0]);
        }

        return $Link;
    }

    #
    # ~
    #

    private $currentAbreviation;
    private $currentMeaning;

    protected function insertAbreviation(array $Element)
    {
        if (isset($Element['text']))
        {
            $Element['elements'] = self::pregReplaceElements(
                '/\b'.preg_quote($this->currentAbreviation, '/').'\b/',
                array(
                    array(
                        'name' => 'abbr',
                        'attributes' => array(
                            'title' => $this->currentMeaning,
                        ),
                        'text' => $this->currentAbreviation,
                    )
                ),
                $Element['text']
            );

            unset($Element['text']);
        }

        return $Element;
    }

    protected function inlineText($text)
    {
        $Inline = $this->inlineTextParent($text);

        if (isset($this->DefinitionData['Abbreviation']))
        {
            foreach ($this->DefinitionData['Abbreviation'] as $abbreviation => $meaning)
            {
                $this->currentAbreviation = $abbreviation;
                $this->currentMeaning = $meaning;

                $Inline['element'] = $this->elementApplyRecursiveDepthFirst(
                    array($this, 'insertAbreviation'),
                    $Inline['element']
                );
            }
        }

        return $Inline;
    }

    #
    # Util Methods
    #

    protected function addDdElement(array $Line, array $Block)
    {
        $text = substr($Line['text'], 1);
        $text = trim($text);

        unset($Block['dd']);

        $Block['dd'] = array(
            'name' => 'dd',
            'handler' => array(
                'function' => 'lineElements',
                'argument' => $text,
                'destination' => 'elements'
            ),
        );

        if (isset($Block['interrupted']))
        {
            $Block['dd']['handler']['function'] = 'textElements';

            unset($Block['interrupted']);
        }

        $Block['element']['elements'] []= & $Block['dd'];

        return $Block;
    }

    protected function buildFootnoteElement()
    {
        $Element = array(
            'name' => 'div',
            'attributes' => array('class' => 'footnotes'),
            'elements' => array(
                array('name' => 'hr'),
                array(
                    'name' => 'ol',
                    'elements' => array(),
                ),
            ),
        );

        uasort($this->DefinitionData['Footnote'], 'self::sortFootnotes');

        foreach ($this->DefinitionData['Footnote'] as $definitionId => $DefinitionData)
        {
            if ( ! isset($DefinitionData['number']))
            {
                continue;
            }

            $text = $DefinitionData['text'];

            $textElements = $this->textElements($text);

            $numbers = range(1, $DefinitionData['count']);

            $backLinkElements = array();

            foreach ($numbers as $number)
            {
                $backLinkElements[] = array('text' => ' ');
                $backLinkElements[] = array(
                    'name' => 'a',
                    'attributes' => array(
                        'href' => "#fnref$number:$definitionId",
                        'rev' => 'footnote',
                        'class' => 'footnote-backref',
                    ),
                    'rawHtml' => '&#8617;',
                    'allowRawHtmlInSafeMode' => true,
                    'autobreak' => false,
                );
            }

            unset($backLinkElements[0]);

            $n = count($textElements) -1;

            if ($textElements[$n]['name'] === 'p')
            {
                $backLinkElements = array_merge(
                    array(
                        array(
                            'rawHtml' => '&#160;',
                            'allowRawHtmlInSafeMode' => true,
                        ),
                    ),
                    $backLinkElements
                );

                unset($textElements[$n]['name']);

                $textElements[$n] = array(
                    'name' => 'p',
                    'elements' => array_merge(
                        array($textElements[$n]),
                        $backLinkElements
                    ),
                );
            }
            else
            {
                $textElements[] = array(
                    'name' => 'p',
                    'elements' => $backLinkElements
                );
            }

            $Element['elements'][1]['elements'] []= array(
                'name' => 'li',
                'attributes' => array('id' => 'fn:'.$definitionId),
                'elements' => array_merge(
                    $textElements
                ),
            );
        }

        return $Element;
    }

    # ~

    protected function parseAttributeDataBase($attributeString)
    {
        $Data = array();

        $attributes = preg_split('/[ ]+/', $attributeString, - 1, PREG_SPLIT_NO_EMPTY);

        foreach ($attributes as $attribute)
        {
            if ($attribute[0] === '#')
            {
                $Data['id'] = substr($attribute, 1);
            }
            else # "."
            {
                $classes []= substr($attribute, 1);
            }
        }

        if (isset($classes))
        {
            $Data['class'] = implode(' ', $classes);
        }

        return $Data;
    }

    # ~

    protected function processTag($elementMarkup) # recursive
    {
        # http://stackoverflow.com/q/1148928/200145
        libxml_use_internal_errors(true);

        $DOMDocument = new \DOMDocument;

        # http://stackoverflow.com/q/11309194/200145
        $elementMarkup = mb_convert_encoding($elementMarkup, 'HTML-ENTITIES', 'UTF-8');

        # http://stackoverflow.com/q/4879946/200145
        $DOMDocument->loadHTML($elementMarkup);
        $DOMDocument->removeChild($DOMDocument->doctype);
        $DOMDocument->replaceChild($DOMDocument->firstChild->firstChild->firstChild, $DOMDocument->firstChild);

        $elementText = '';

        if ($DOMDocument->documentElement->getAttribute('markdown') === '1')
        {
            foreach ($DOMDocument->documentElement->childNodes as $Node)
            {
                $elementText .= $DOMDocument->saveHTML($Node);
            }

            $DOMDocument->documentElement->removeAttribute('markdown');

            $elementText = "\n".$this->text($elementText)."\n";
        }
        else
        {
            foreach ($DOMDocument->documentElement->childNodes as $Node)
            {
                $nodeMarkup = $DOMDocument->saveHTML($Node);

                if ($Node instanceof \DOMElement and ! in_array($Node->nodeName, $this->textLevelElements))
                {
                    $elementText .= $this->processTag($nodeMarkup);
                }
                else
                {
                    $elementText .= $nodeMarkup;
                }
            }
        }

        # because we don't want for markup to get encoded
        $DOMDocument->documentElement->nodeValue = 'placeholder\x1A';

        $markup = $DOMDocument->saveHTML($DOMDocument->documentElement);
        $markup = str_replace('placeholder\x1A', $elementText, $markup);

        return $markup;
    }

    # ~

    protected function sortFootnotes($A, $B) # callback
    {
        return $A['number'] - $B['number'];
    }

    #
    # Fields
    #

    protected $regexAttribute = '(?:[#.][-\w]+[ ]*)';

    protected function textElements($text)
    {
        # make sure no definitions are set
        $this->DefinitionData = array();

        # standardize line breaks
        $text = str_replace(array("\r\n", "\r"), "\n", $text);

        # remove surrounding line breaks
        $text = trim($text, "\n");

        # split text into lines
        $lines = explode("\n", $text);

        # iterate through lines to identify blocks
        return $this->linesElements($lines);
    }

    #
    # Setters
    #

    function setBreaksEnabled($breaksEnabled)
    {
        $this->breaksEnabled = $breaksEnabled;

        return $this;
    }

    protected $breaksEnabled;

    function setMarkupEscaped($markupEscaped)
    {
        $this->markupEscaped = $markupEscaped;

        return $this;
    }

    protected $markupEscaped;

    function setUrlsLinked($urlsLinked)
    {
        $this->urlsLinked = $urlsLinked;

        return $this;
    }

    protected $urlsLinked = true;

    function setSafeMode($safeMode)
    {
        $this->safeMode = (bool) $safeMode;

        return $this;
    }

    protected $safeMode;

    function setStrictMode($strictMode)
    {
        $this->strictMode = (bool) $strictMode;

        return $this;
    }

    protected $strictMode;

    protected $safeLinksWhitelist = array(
        'http://',
        'https://',
        'ftp://',
        'ftps://',
        'mailto:',
        'tel:',
        'data:image/png;base64,',
        'data:image/gif;base64,',
        'data:image/jpeg;base64,',
        'irc:',
        'ircs:',
        'git:',
        'ssh:',
        'news:',
        'steam:',
    );

    #
    # Lines
    #

    protected $BlockTypes = array(
        '#' => array('Header'),
        '*' => array('Rule', 'List', 'Abbreviation'),
        '+' => array('List'),
        '-' => array('SetextHeader', 'Table', 'Rule', 'List'),
        '0' => array('List'),
        '1' => array('List'),
        '2' => array('List'),
        '3' => array('List'),
        '4' => array('List'),
        '5' => array('List'),
        '6' => array('List'),
        '7' => array('List'),
        '8' => array('List'),
        '9' => array('List'),
        ':' => array('Table', 'DefinitionList'),
        '<' => array('Comment', 'Markup'),
        '=' => array('SetextHeader'),
        '>' => array('Quote'),
        '[' => array('Footnote', 'Reference'),
        '_' => array('Rule'),
        '`' => array('FencedCode'),
        '|' => array('Table'),
        '~' => array('FencedCode'),
    );

    # ~

    protected $unmarkedBlockTypes = array(
        'Code',
    );

    #
    # Blocks
    #

    protected function lines(array $lines)
    {
        return $this->elements($this->linesElements($lines));
    }

    protected function linesElements(array $lines)
    {
        $Elements = array();
        $CurrentBlock = null;

        foreach ($lines as $line)
        {
            if (chop($line) === '')
            {
                if (isset($CurrentBlock))
                {
                    $CurrentBlock['interrupted'] = (isset($CurrentBlock['interrupted'])
                        ? $CurrentBlock['interrupted'] + 1 : 1
                    );
                }

                continue;
            }

            while (($beforeTab = strstr($line, "\t", true)) !== false)
            {
                $shortage = 4 - mb_strlen($beforeTab, 'utf-8') % 4;

                $line = $beforeTab
                    . str_repeat(' ', $shortage)
                    . substr($line, strlen($beforeTab) + 1)
                ;
            }

            $indent = strspn($line, ' ');

            $text = $indent > 0 ? substr($line, $indent) : $line;

            # ~

            $Line = array('body' => $line, 'indent' => $indent, 'text' => $text);

            # ~

            if (isset($CurrentBlock['continuable']))
            {
                $methodName = 'block' . $CurrentBlock['type'] . 'Continue';
                $Block = $this->$methodName($Line, $CurrentBlock);

                if (isset($Block))
                {
                    $CurrentBlock = $Block;

                    continue;
                }
                else
                {
                    if ($this->isBlockCompletable($CurrentBlock['type']))
                    {
                        $methodName = 'block' . $CurrentBlock['type'] . 'Complete';
                        $CurrentBlock = $this->$methodName($CurrentBlock);
                    }
                }
            }

            # ~

            $marker = $text[0];

            # ~

            $blockTypes = $this->unmarkedBlockTypes;

            if (isset($this->BlockTypes[$marker]))
            {
                foreach ($this->BlockTypes[$marker] as $blockType)
                {
                    $blockTypes []= $blockType;
                }
            }

            #
            # ~

            foreach ($blockTypes as $blockType)
            {
                $Block = $this->{"block$blockType"}($Line, $CurrentBlock);

                if (isset($Block))
                {
                    $Block['type'] = $blockType;

                    if ( ! isset($Block['identified']))
                    {
                        if (isset($CurrentBlock))
                        {
                            $Elements[] = $this->extractElement($CurrentBlock);
                        }

                        $Block['identified'] = true;
                    }

                    if ($this->isBlockContinuable($blockType))
                    {
                        $Block['continuable'] = true;
                    }

                    $CurrentBlock = $Block;

                    continue 2;
                }
            }

            # ~

            if (isset($CurrentBlock) and $CurrentBlock['type'] === 'Paragraph')
            {
                $Block = $this->paragraphContinue($Line, $CurrentBlock);
            }

            if (isset($Block))
            {
                $CurrentBlock = $Block;
            }
            else
            {
                if (isset($CurrentBlock))
                {
                    $Elements[] = $this->extractElement($CurrentBlock);
                }

                $CurrentBlock = $this->paragraph($Line);

                $CurrentBlock['identified'] = true;
            }
        }

        # ~

        if (isset($CurrentBlock['continuable']) and $this->isBlockCompletable($CurrentBlock['type']))
        {
            $methodName = 'block' . $CurrentBlock['type'] . 'Complete';
            $CurrentBlock = $this->$methodName($CurrentBlock);
        }

        # ~

        if (isset($CurrentBlock))
        {
            $Elements[] = $this->extractElement($CurrentBlock);
        }

        # ~

        return $Elements;
    }

    protected function extractElement(array $Component)
    {
        if ( ! isset($Component['element']))
        {
            if (isset($Component['markup']))
            {
                $Component['element'] = array('rawHtml' => $Component['markup']);
            }
            elseif (isset($Component['hidden']))
            {
                $Component['element'] = array();
            }
        }

        return $Component['element'];
    }

    protected function isBlockContinuable($Type)
    {
        return method_exists($this, 'block' . $Type . 'Continue');
    }

    protected function isBlockCompletable($Type)
    {
        return method_exists($this, 'block' . $Type . 'Complete');
    }

    #
    # Code

    protected function blockCodeBase($Line, $Block = null)
    {
        if (isset($Block) and $Block['type'] === 'Paragraph' and ! isset($Block['interrupted']))
        {
            return;
        }

        if ($Line['indent'] >= 4)
        {
            $text = substr($Line['body'], 4);

            $Block = array(
                'element' => array(
                    'name' => 'pre',
                    'element' => array(
                        'name' => 'code',
                        'text' => $text,
                    ),
                ),
            );

            return $Block;
        }
    }

    protected function blockCodeContinue($Line, $Block)
    {
        if ($Line['indent'] >= 4)
        {
            if (isset($Block['interrupted']))
            {
                $Block['element']['element']['text'] .= str_repeat("\n", $Block['interrupted']);

                unset($Block['interrupted']);
            }

            $Block['element']['element']['text'] .= "\n";

            $text = substr($Line['body'], 4);

            $Block['element']['element']['text'] .= $text;

            return $Block;
        }
    }

    protected function blockCodeComplete($Block)
    {
        return $Block;
    }

    #
    # Comment

    protected function blockCommentBase($Line)
    {
        if ($this->markupEscaped or $this->safeMode)
        {
            return;
        }

        if (strpos($Line['text'], '<!--') === 0)
        {
            $Block = array(
                'element' => array(
                    'rawHtml' => $Line['body'],
                    'autobreak' => true,
                ),
            );

            if (strpos($Line['text'], '-->') !== false)
            {
                $Block['closed'] = true;
            }

            return $Block;
        }
    }

    protected function blockCommentContinue($Line, array $Block)
    {
        if (isset($Block['closed']))
        {
            return;
        }

        $Block['element']['rawHtml'] .= "\n" . $Line['body'];

        if (strpos($Line['text'], '-->') !== false)
        {
            $Block['closed'] = true;
        }

        return $Block;
    }

    #
    # Fenced Code

    protected function blockFencedCodeBase($Line)
    {
        $marker = $Line['text'][0];

        $openerLength = strspn($Line['text'], $marker);

        if ($openerLength < 3)
        {
            return;
        }

        $infostring = trim(substr($Line['text'], $openerLength), "\t ");

        if (strpos($infostring, '`') !== false)
        {
            return;
        }

        $Element = array(
            'name' => 'code',
            'text' => '',
        );

        if ($infostring !== '')
        {
            /**
             * https://www.w3.org/TR/2011/WD-html5-20110525/elements.html#classes
             * Every HTML element may have a class attribute specified.
             * The attribute, if specified, must have a value that is a set
             * of space-separated tokens representing the various classes
             * that the element belongs to.
             * [...]
             * The space characters, for the purposes of this specification,
             * are U+0020 SPACE, U+0009 CHARACTER TABULATION (tab),
             * U+000A LINE FEED (LF), U+000C FORM FEED (FF), and
             * U+000D CARRIAGE RETURN (CR).
             */
            $language = substr($infostring, 0, strcspn($infostring, " \t\n\f\r"));

            $Element['attributes'] = array('class' => "language-$language");
        }

        $Block = array(
            'char' => $marker,
            'openerLength' => $openerLength,
            'element' => array(
                'name' => 'pre',
                'element' => $Element,
            ),
        );

        return $Block;
    }

    protected function blockFencedCodeContinue($Line, $Block)
    {
        if (isset($Block['complete']))
        {
            return;
        }

        if (isset($Block['interrupted']))
        {
            $Block['element']['element']['text'] .= str_repeat("\n", $Block['interrupted']);

            unset($Block['interrupted']);
        }

        if (($len = strspn($Line['text'], $Block['char'])) >= $Block['openerLength']
            and chop(substr($Line['text'], $len), ' ') === ''
        ) {
            $Block['element']['element']['text'] = substr($Block['element']['element']['text'], 1);

            $Block['complete'] = true;

            return $Block;
        }

        $Block['element']['element']['text'] .= "\n" . $Line['body'];

        return $Block;
    }

    protected function blockFencedCodeComplete($Block)
    {
        return $Block;
    }

    #
    # Header

    protected function blockHeaderParent($Line)
    {
        $level = strspn($Line['text'], '#');

        if ($level > 6)
        {
            return;
        }

        $text = trim($Line['text'], '#');

        if ($this->strictMode and isset($text[0]) and $text[0] !== ' ')
        {
            return;
        }

        $text = trim($text, ' ');

        $Block = array(
            'element' => array(
                'name' => 'h' . $level,
                'handler' => array(
                    'function' => 'lineElements',
                    'argument' => $text,
                    'destination' => 'elements',
                )
            ),
        );

        return $Block;
    }

    #
    # List

    protected function blockListBase($Line, array $CurrentBlock = null)
    {
        list($name, $pattern) = $Line['text'][0] <= '-' ? array('ul', '[*+-]') : array('ol', '[0-9]{1,9}+[.\)]');

        if (preg_match('/^('.$pattern.'([ ]++|$))(.*+)/', $Line['text'], $matches))
        {
            $contentIndent = strlen($matches[2]);

            if ($contentIndent >= 5)
            {
                $contentIndent -= 1;
                $matches[1] = substr($matches[1], 0, -$contentIndent);
                $matches[3] = str_repeat(' ', $contentIndent) . $matches[3];
            }
            elseif ($contentIndent === 0)
            {
                $matches[1] .= ' ';
            }

            $markerWithoutWhitespace = strstr($matches[1], ' ', true);

            $Block = array(
                'indent' => $Line['indent'],
                'pattern' => $pattern,
                'data' => array(
                    'type' => $name,
                    'marker' => $matches[1],
                    'markerType' => ($name === 'ul' ? $markerWithoutWhitespace : substr($markerWithoutWhitespace, -1)),
                ),
                'element' => array(
                    'name' => $name,
                    'elements' => array(),
                ),
            );
            $Block['data']['markerTypeRegex'] = preg_quote($Block['data']['markerType'], '/');

            if ($name === 'ol')
            {
                $listStart = ltrim(strstr($matches[1], $Block['data']['markerType'], true), '0') ?: '0';

                if ($listStart !== '1')
                {
                    if (
                        isset($CurrentBlock)
                        and $CurrentBlock['type'] === 'Paragraph'
                        and ! isset($CurrentBlock['interrupted'])
                    ) {
                        return;
                    }

                    $Block['element']['attributes'] = array('start' => $listStart);
                }
            }

            $Block['li'] = array(
                'name' => 'li',
                'handler' => array(
                    'function' => 'li',
                    'argument' => !empty($matches[3]) ? array($matches[3]) : array(),
                    'destination' => 'elements'
                )
            );

            $Block['element']['elements'] []= & $Block['li'];

            return $Block;
        }
    }

    protected function blockListContinue($Line, array $Block)
    {
        if (isset($Block['interrupted']) and empty($Block['li']['handler']['argument']))
        {
            return null;
        }

        $requiredIndent = ($Block['indent'] + strlen($Block['data']['marker']));

        if ($Line['indent'] < $requiredIndent
            and (
                (
                    $Block['data']['type'] === 'ol'
                    and preg_match('/^[0-9]++'.$Block['data']['markerTypeRegex'].'(?:[ ]++(.*)|$)/', $Line['text'], $matches)
                ) or (
                    $Block['data']['type'] === 'ul'
                    and preg_match('/^'.$Block['data']['markerTypeRegex'].'(?:[ ]++(.*)|$)/', $Line['text'], $matches)
                )
            )
        ) {
            if (isset($Block['interrupted']))
            {
                $Block['li']['handler']['argument'] []= '';

                $Block['loose'] = true;

                unset($Block['interrupted']);
            }

            unset($Block['li']);

            $text = isset($matches[1]) ? $matches[1] : '';

            $Block['indent'] = $Line['indent'];

            $Block['li'] = array(
                'name' => 'li',
                'handler' => array(
                    'function' => 'li',
                    'argument' => array($text),
                    'destination' => 'elements'
                )
            );

            $Block['element']['elements'] []= & $Block['li'];

            return $Block;
        }
        elseif ($Line['indent'] < $requiredIndent and $this->blockList($Line))
        {
            return null;
        }

        if ($Line['text'][0] === '[' and $this->blockReference($Line))
        {
            return $Block;
        }

        if ($Line['indent'] >= $requiredIndent)
        {
            if (isset($Block['interrupted']))
            {
                $Block['li']['handler']['argument'] []= '';

                $Block['loose'] = true;

                unset($Block['interrupted']);
            }

            $text = substr($Line['body'], $requiredIndent);

            $Block['li']['handler']['argument'] []= $text;

            return $Block;
        }

        if ( ! isset($Block['interrupted']))
        {
            $text = preg_replace('/^[ ]{0,'.$requiredIndent.'}+/', '', $Line['body']);

            $Block['li']['handler']['argument'] []= $text;

            return $Block;
        }
    }

    protected function blockListComplete(array $Block)
    {
        if (isset($Block['loose']))
        {
            foreach ($Block['element']['elements'] as &$li)
            {
                if (end($li['handler']['argument']) !== '')
                {
                    $li['handler']['argument'] []= '';
                }
            }
        }

        return $Block;
    }

    #
    # Quote

    protected function blockQuoteBase($Line)
    {
        if (preg_match('/^>[ ]?+(.*+)/', $Line['text'], $matches))
        {
            $Block = array(
                'element' => array(
                    'name' => 'blockquote',
                    'handler' => array(
                        'function' => 'linesElements',
                        'argument' => (array) $matches[1],
                        'destination' => 'elements',
                    )
                ),
            );

            return $Block;
        }
    }

    protected function blockQuoteContinue($Line, array $Block)
    {
        if (isset($Block['interrupted']))
        {
            return;
        }

        if ($Line['text'][0] === '>' and preg_match('/^>[ ]?+(.*+)/', $Line['text'], $matches))
        {
            $Block['element']['handler']['argument'] []= $matches[1];

            return $Block;
        }

        if ( ! isset($Block['interrupted']))
        {
            $Block['element']['handler']['argument'] []= $Line['text'];

            return $Block;
        }
    }

    #
    # Rule

    protected function blockRuleBase($Line)
    {
        $marker = $Line['text'][0];

        if (substr_count($Line['text'], $marker) >= 3 and chop($Line['text'], " $marker") === '')
        {
            $Block = array(
                'element' => array(
                    'name' => 'hr',
                ),
            );

            return $Block;
        }
    }

    #
    # Setext

    protected function blockSetextHeaderParent($Line, array $Block = null)
    {
        if ( ! isset($Block) or $Block['type'] !== 'Paragraph' or isset($Block['interrupted']))
        {
            return;
        }

        if ($Line['indent'] < 4 and chop(chop($Line['text'], ' '), $Line['text'][0]) === '')
        {
            $Block['element']['name'] = $Line['text'][0] === '=' ? 'h1' : 'h2';

            return $Block;
        }
    }

    #
    # Reference

    protected function blockReferenceBase($Line)
    {
        if (strpos($Line['text'], ']') !== false
            and preg_match('/^\[(.+?)\]:[ ]*+<?(\S+?)>?(?:[ ]+["\'(](.+)["\')])?[ ]*+$/', $Line['text'], $matches)
        ) {
            $id = strtolower($matches[1]);

            $Data = array(
                'url' => UriFactory::build($matches[2]),
                'title' => isset($matches[3]) ? $matches[3] : null,
            );

            $this->DefinitionData['Reference'][$id] = $Data;

            $Block = array(
                'element' => array(),
            );

            return $Block;
        }
    }

    #
    # Table

    protected function blockTableBase($Line, array $Block = null)
    {
        if ( ! isset($Block) or $Block['type'] !== 'Paragraph' or isset($Block['interrupted']))
        {
            return;
        }

        if (
            strpos($Block['element']['handler']['argument'], '|') === false
            and strpos($Line['text'], '|') === false
            and strpos($Line['text'], ':') === false
            or strpos($Block['element']['handler']['argument'], "\n") !== false
        ) {
            return;
        }

        if (chop($Line['text'], ' -:|') !== '')
        {
            return;
        }

        $alignments = array();

        $divider = $Line['text'];

        $divider = trim($divider);
        $divider = trim($divider, '|');

        $dividerCells = explode('|', $divider);

        foreach ($dividerCells as $dividerCell)
        {
            $dividerCell = trim($dividerCell);

            if ($dividerCell === '')
            {
                return;
            }

            $alignment = null;

            if ($dividerCell[0] === ':')
            {
                $alignment = 'left';
            }

            if (substr($dividerCell, - 1) === ':')
            {
                $alignment = $alignment === 'left' ? 'center' : 'right';
            }

            $alignments []= $alignment;
        }

        # ~

        $HeaderElements = array();

        $header = $Block['element']['handler']['argument'];

        $header = trim($header);
        $header = trim($header, '|');

        $headerCells = explode('|', $header);

        if (count($headerCells) !== count($alignments))
        {
            return;
        }

        foreach ($headerCells as $index => $headerCell)
        {
            $headerCell = trim($headerCell);

            $HeaderElement = array(
                'name' => 'th',
                'handler' => array(
                    'function' => 'lineElements',
                    'argument' => $headerCell,
                    'destination' => 'elements',
                )
            );

            if (isset($alignments[$index]))
            {
                $alignment = $alignments[$index];

                $HeaderElement['attributes'] = array(
                    'style' => "text-align: $alignment;",
                );
            }

            $HeaderElements []= $HeaderElement;
        }

        # ~

        $Block = array(
            'alignments' => $alignments,
            'identified' => true,
            'element' => array(
                'name' => 'table',
                'elements' => array(),
            ),
        );

        $Block['element']['elements'] []= array(
            'name' => 'thead',
        );

        $Block['element']['elements'] []= array(
            'name' => 'tbody',
            'elements' => array(),
        );

        $Block['element']['elements'][0]['elements'] []= array(
            'name' => 'tr',
            'elements' => $HeaderElements,
        );

        return $Block;
    }

    protected function blockTableContinue($Line, array $Block)
    {
        if (isset($Block['interrupted']))
        {
            return;
        }

        if (count($Block['alignments']) === 1 or $Line['text'][0] === '|' or strpos($Line['text'], '|'))
        {
            $Elements = array();

            $row = $Line['text'];

            $row = trim($row);
            $row = trim($row, '|');

            preg_match_all('/(?:(\\\\[|])|[^|`]|`[^`]++`|`)++/', $row, $matches);

            $cells = array_slice($matches[0], 0, count($Block['alignments']));

            foreach ($cells as $index => $cell)
            {
                $cell = trim($cell);

                $Element = array(
                    'name' => 'td',
                    'handler' => array(
                        'function' => 'lineElements',
                        'argument' => $cell,
                        'destination' => 'elements',
                    )
                );

                if (isset($Block['alignments'][$index]))
                {
                    $Element['attributes'] = array(
                        'style' => 'text-align: ' . $Block['alignments'][$index] . ';',
                    );
                }

                $Elements []= $Element;
            }

            $Element = array(
                'name' => 'tr',
                'elements' => $Elements,
            );

            $Block['element']['elements'][1]['elements'] []= $Element;

            return $Block;
        }
    }

    #
    # ~
    #

    protected function paragraph($Line)
    {
        return array(
            'type' => 'Paragraph',
            'element' => array(
                'name' => 'p',
                'handler' => array(
                    'function' => 'lineElements',
                    'argument' => $Line['text'],
                    'destination' => 'elements',
                ),
            ),
        );
    }

    protected function paragraphContinue($Line, array $Block)
    {
        if (isset($Block['interrupted']))
        {
            return;
        }

        $Block['element']['handler']['argument'] .= "\n".$Line['text'];

        return $Block;
    }

    #
    # Inline Elements
    #

    protected $InlineTypes = array(
        '!' => array('Image'),
        '&' => array('SpecialCharacter'),
        '*' => array('Emphasis'),
        ':' => array('Url'),
        '<' => array('UrlTag', 'EmailTag', 'Markup'),
        '[' => array('FootnoteMarker', 'Link'),
        '_' => array('Emphasis'),
        '`' => array('Code'),
        '~' => array('Strikethrough'),
        '\\' => array('EscapeSequence'),
    );

    # ~

    protected $inlineMarkerList = '!*_&[:<`~\\';

    #
    # ~
    #

    public function line($text, $nonNestables = array())
    {
        return $this->elements($this->lineElements($text, $nonNestables));
    }

    /*
    protected function lineElements($text, $nonNestables = array())
    {
        # standardize line breaks
        $text = str_replace(array("\r\n", "\r"), "\n", $text);

        $Elements = array();

        $nonNestables = (empty($nonNestables)
            ? array()
            : array_combine($nonNestables, $nonNestables)
        );

        # $excerpt is based on the first occurrence of a marker

        while ($excerpt = strpbrk($text, $this->inlineMarkerList))
        {
            $marker = $excerpt[0];

            $markerPosition = strlen($text) - strlen($excerpt);

            $Excerpt = array('text' => $excerpt, 'context' => $text);

            foreach ($this->InlineTypes[$marker] as $inlineType)
            {
                # check to see if the current inline type is nestable in the current context

                if (isset($nonNestables[$inlineType]))
                {
                    continue;
                }

                $Inline = $this->{"inline$inlineType"}($Excerpt);

                if ( ! isset($Inline))
                {
                    continue;
                }

                # makes sure that the inline belongs to "our" marker

                if (isset($Inline['position']) and $Inline['position'] > $markerPosition)
                {
                    continue;
                }

                # sets a default inline position

                if ( ! isset($Inline['position']))
                {
                    $Inline['position'] = $markerPosition;
                }

                # cause the new element to 'inherit' our non nestables


                $Inline['element']['nonNestables'] = isset($Inline['element']['nonNestables'])
                    ? array_merge($Inline['element']['nonNestables'], $nonNestables)
                    : $nonNestables
                ;

                # the text that comes before the inline
                $unmarkedText = substr($text, 0, $Inline['position']);

                # compile the unmarked text
                $InlineText = $this->inlineText($unmarkedText);
                $Elements[] = $InlineText['element'];

                # compile the inline
                $Elements[] = $this->extractElement($Inline);

                # remove the examined text
                $text = substr($text, $Inline['position'] + $Inline['extent']);

                continue 2;
            }

            # the marker does not belong to an inline

            $unmarkedText = substr($text, 0, $markerPosition + 1);

            $InlineText = $this->inlineText($unmarkedText);
            $Elements[] = $InlineText['element'];

            $text = substr($text, $markerPosition + 1);
        }

        $InlineText = $this->inlineText($text);
        $Elements[] = $InlineText['element'];

        foreach ($Elements as &$Element)
        {
            if ( ! isset($Element['autobreak']))
            {
                $Element['autobreak'] = false;
            }
        }

        return $Elements;
    }
    */

    #
    # ~
    #

    protected function inlineTextParent($text)
    {
        $Inline = array(
            'extent' => strlen($text),
            'element' => array(),
        );

        $Inline['element']['elements'] = self::pregReplaceElements(
            $this->breaksEnabled ? '/[ ]*+\n/' : '/(?:[ ]*+\\\\|[ ]{2,}+)\n/',
            array(
                array('name' => 'br'),
                array('text' => "\n"),
            ),
            $text
        );

        return $Inline;
    }

    protected function inlineCodeParent($Excerpt)
    {
        $marker = $Excerpt['text'][0];

        if (preg_match('/^(['.$marker.']++)[ ]*+(.+?)[ ]*+(?<!['.$marker.'])\1(?!'.$marker.')/s', $Excerpt['text'], $matches))
        {
            $text = $matches[2];
            $text = preg_replace('/[ ]*+\n/', ' ', $text);

            return array(
                'extent' => strlen($matches[0]),
                'element' => array(
                    'name' => 'code',
                    'text' => $text,
                ),
            );
        }
    }

    protected function inlineEmailTagParent($Excerpt)
    {
        $hostnameLabel = '[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?';

        $commonMarkEmail = '[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]++@'
            . $hostnameLabel . '(?:\.' . $hostnameLabel . ')*';

        if (strpos($Excerpt['text'], '>') !== false
            and preg_match("/^<((mailto:)?$commonMarkEmail)>/i", $Excerpt['text'], $matches)
        ){
            $url = $matches[1];

            if ( ! isset($matches[2]))
            {
                $url = "mailto:$url";
            }

            return array(
                'extent' => strlen($matches[0]),
                'element' => array(
                    'name' => 'a',
                    'text' => $matches[1],
                    'attributes' => array(
                        'href' => $url,
                    ),
                ),
            );
        }
    }

    protected function inlineEmphasisParent($Excerpt)
    {
        if ( ! isset($Excerpt['text'][1]))
        {
            return;
        }

        $marker = $Excerpt['text'][0];

        if ($Excerpt['text'][1] === $marker && isset($this->StrongRegex[$marker]) and preg_match($this->StrongRegex[$marker], $Excerpt['text'], $matches))
        {
            $emphasis = 'strong';
        }
        elseif ($Excerpt['text'][1] === $marker && isset($this->UnderlineRegex[$marker]) and preg_match($this->UnderlineRegex[$marker], $Excerpt['text'], $matches))
        {
            $emphasis = 'u';
        }
        elseif (preg_match($this->EmRegex[$marker], $Excerpt['text'], $matches))
        {
            $emphasis = 'em';
        }
        else
        {
            return;
        }

        return array(
            'extent' => strlen($matches[0]),
            'element' => array(
                'name' => $emphasis,
                'handler' => array(
                    'function' => 'lineElements',
                    'argument' => $matches[1],
                    'destination' => 'elements',
                )
            ),
        );
    }

    /*
    protected function inlineEscapeSequenceParent($Excerpt)
    {
        if (isset($Excerpt['text'][1]) and in_array($Excerpt['text'][1], $this->specialCharacters))
        {
            return array(
                'element' => array('rawHtml' => $Excerpt['text'][1]),
                'extent' => 2,
            );
        }
    }
    */

    protected function inlineImageParent($Excerpt)
    {
        if ( ! isset($Excerpt['text'][1]) or $Excerpt['text'][1] !== '[')
        {
            return;
        }

        $Excerpt['text']= substr($Excerpt['text'], 1);

        $Link = $this->inlineLink($Excerpt);

        if ($Link === null)
        {
            return;
        }

        $Inline = array(
            'extent' => $Link['extent'] + 1,
            'element' => array(
                'name' => 'img',
                'attributes' => array(
                    'src' => $Link['element']['attributes']['href'],
                    'alt' => $Link['element']['handler']['argument'],
                ),
                'autobreak' => true,
            ),
        );

        $Inline['element']['attributes'] += $Link['element']['attributes'];

        unset($Inline['element']['attributes']['href']);

        return $Inline;
    }

    protected function inlineLinkParent($Excerpt)
    {
        $Element = array(
            'name' => 'a',
            'handler' => array(
                'function' => 'lineElements',
                'argument' => null,
                'destination' => 'elements',
            ),
            'nonNestables' => array('Url', 'Link'),
            'attributes' => array(
                'href' => null,
                'title' => null,
            ),
        );

        $extent = 0;

        $remainder = $Excerpt['text'];

        if (preg_match('/\[((?:[^][]++|(?R))*+)\]/', $remainder, $matches))
        {
            $Element['handler']['argument'] = $matches[1];

            $extent += strlen($matches[0]);

            $remainder = substr($remainder, $extent);
        }
        else
        {
            return;
        }

        if (preg_match('/^[(]\s*+((?:[^ ()]++|[(][^ )]+[)])++)(?:[ ]+("[^"]*+"|\'[^\']*+\'))?\s*+[)]/', $remainder, $matches))
        {
            $Element['attributes']['href'] = $matches[1];

            if (isset($matches[2]))
            {
                $Element['attributes']['title'] = substr($matches[2], 1, - 1);
            }

            $extent += strlen($matches[0]);
        }
        else
        {
            if (preg_match('/^\s*\[(.*?)\]/', $remainder, $matches))
            {
                $definition = strlen($matches[1]) ? $matches[1] : $Element['handler']['argument'];
                $definition = strtolower($definition);

                $extent += strlen($matches[0]);
            }
            else
            {
                $definition = strtolower($Element['handler']['argument']);
            }

            if ( ! isset($this->DefinitionData['Reference'][$definition]))
            {
                return;
            }

            $Definition = $this->DefinitionData['Reference'][$definition];

            $Element['attributes']['href'] = $Definition['url'];
            $Element['attributes']['title'] = $Definition['title'];
        }

        return array(
            'extent' => $extent,
            'element' => $Element,
        );
    }

    protected function inlineMarkupBase($Excerpt)
    {
        if ($this->markupEscaped or $this->safeMode or strpos($Excerpt['text'], '>') === false)
        {
            return;
        }

        if ($Excerpt['text'][1] === '/' and preg_match('/^<\/\w[\w-]*+[ ]*+>/s', $Excerpt['text'], $matches))
        {
            return array(
                'element' => array('rawHtml' => $matches[0]),
                'extent' => strlen($matches[0]),
            );
        }

        if ($Excerpt['text'][1] === '!' and preg_match('/^<!---?[^>-](?:-?+[^-])*-->/s', $Excerpt['text'], $matches))
        {
            return array(
                'element' => array('rawHtml' => $matches[0]),
                'extent' => strlen($matches[0]),
            );
        }

        if ($Excerpt['text'][1] !== ' ' and preg_match('/^<\w[\w-]*+(?:[ ]*+'.$this->regexHtmlAttribute.')*+[ ]*+\/?>/s', $Excerpt['text'], $matches))
        {
            return array(
                'element' => array('rawHtml' => $matches[0]),
                'extent' => strlen($matches[0]),
            );
        }
    }

    protected function inlineSpecialCharacter($Excerpt)
    {
        if (substr($Excerpt['text'], 1, 1) !== ' ' and strpos($Excerpt['text'], ';') !== false
            and preg_match('/^&(#?+[0-9a-zA-Z]++);/', $Excerpt['text'], $matches)
        ) {
            return array(
                'element' => array('rawHtml' => '&' . $matches[1] . ';'),
                'extent' => strlen($matches[0]),
            );
        }

        return;
    }

    protected function inlineStrikethroughBase($Excerpt)
    {
        if ( ! isset($Excerpt['text'][1]))
        {
            return;
        }

        if ($Excerpt['text'][1] === '~' and preg_match('/^~~(?=\S)(.+?)(?<=\S)~~/', $Excerpt['text'], $matches))
        {
            return array(
                'extent' => strlen($matches[0]),
                'element' => array(
                    'name' => 'del',
                    'handler' => array(
                        'function' => 'lineElements',
                        'argument' => $matches[1],
                        'destination' => 'elements',
                    )
                ),
            );
        }
    }

    protected function inlineUrlBase($Excerpt)
    {
        if ($this->urlsLinked !== true or ! isset($Excerpt['text'][2]) or $Excerpt['text'][2] !== '/')
        {
            return;
        }

        if (strpos($Excerpt['context'], 'http') !== false
            and preg_match('/\bhttps?+:[\/]{2}[^\s<]+\b\/*+/ui', $Excerpt['context'], $matches, PREG_OFFSET_CAPTURE)
        ) {
            $url = $matches[0][0];

            $Inline = array(
                'extent' => strlen($matches[0][0]),
                'position' => $matches[0][1],
                'element' => array(
                    'name' => 'a',
                    'text' => $url,
                    'attributes' => array(
                        'href' => $url,
                    ),
                ),
            );

            return $Inline;
        }
    }

    protected function inlineUrlTagBase($Excerpt)
    {
        if (strpos($Excerpt['text'], '>') !== false and preg_match('/^<(\w++:\/{2}[^ >]++)>/i', $Excerpt['text'], $matches))
        {
            $url = $matches[1];

            return array(
                'extent' => strlen($matches[0]),
                'element' => array(
                    'name' => 'a',
                    'text' => $url,
                    'attributes' => array(
                        'href' => $url,
                    ),
                ),
            );
        }
    }

    # ~

    protected function unmarkedText($text)
    {
        $Inline = $this->inlineText($text);
        return $this->element($Inline['element']);
    }

    #
    # Handlers
    #

    protected function handle(array $Element)
    {
        if (isset($Element['handler']))
        {
            if (!isset($Element['nonNestables']))
            {
                $Element['nonNestables'] = array();
            }

            if (is_string($Element['handler']))
            {
                $function = $Element['handler'];
                $argument = $Element['text'];
                unset($Element['text']);
                $destination = 'rawHtml';
            }
            else
            {
                $function = $Element['handler']['function'];
                $argument = $Element['handler']['argument'];
                $destination = $Element['handler']['destination'];
            }

            $Element[$destination] = $this->{$function}($argument, $Element['nonNestables']);

            if ($destination === 'handler')
            {
                $Element = $this->handle($Element);
            }

            unset($Element['handler']);
        }

        return $Element;
    }

    protected function handleElementRecursive(array $Element)
    {
        return $this->elementApplyRecursive(array($this, 'handle'), $Element);
    }

    protected function handleElementsRecursive(array $Elements)
    {
        return $this->elementsApplyRecursive(array($this, 'handle'), $Elements);
    }

    protected function elementApplyRecursive($closure, array $Element)
    {
        $Element = call_user_func($closure, $Element);

        if (isset($Element['elements']))
        {
            $Element['elements'] = $this->elementsApplyRecursive($closure, $Element['elements']);
        }
        elseif (isset($Element['element']))
        {
            $Element['element'] = $this->elementApplyRecursive($closure, $Element['element']);
        }

        return $Element;
    }

    protected function elementApplyRecursiveDepthFirst($closure, array $Element)
    {
        if (isset($Element['elements']))
        {
            $Element['elements'] = $this->elementsApplyRecursiveDepthFirst($closure, $Element['elements']);
        }
        elseif (isset($Element['element']))
        {
            $Element['element'] = $this->elementsApplyRecursiveDepthFirst($closure, $Element['element']);
        }

        $Element = call_user_func($closure, $Element);

        return $Element;
    }

    protected function elementsApplyRecursive($closure, array $Elements)
    {
        foreach ($Elements as &$Element)
        {
            $Element = $this->elementApplyRecursive($closure, $Element);
        }

        return $Elements;
    }

    protected function elementsApplyRecursiveDepthFirst($closure, array $Elements)
    {
        foreach ($Elements as &$Element)
        {
            $Element = $this->elementApplyRecursiveDepthFirst($closure, $Element);
        }

        return $Elements;
    }

    protected function element(array $Element)
    {
        if ($this->safeMode)
        {
            $Element = $this->sanitiseElement($Element);
        }

        # identity map if element has no handler
        $Element = $this->handle($Element);

        $hasName = isset($Element['name']);

        $markup = '';

        if ($hasName)
        {
            $markup .= '<' . $Element['name'];

            if (isset($Element['attributes']))
            {
                foreach ($Element['attributes'] as $name => $value)
                {
                    if ($value === null)
                    {
                        continue;
                    }

                    $markup .= " $name=\"".self::escape($value).'"';
                }
            }
        }

        $permitRawHtml = false;

        if (isset($Element['text']))
        {
            $text = $Element['text'];
        }
        // very strongly consider an alternative if you're writing an
        // extension
        elseif (isset($Element['rawHtml']))
        {
            $text = $Element['rawHtml'];

            $allowRawHtmlInSafeMode = isset($Element['allowRawHtmlInSafeMode']) && $Element['allowRawHtmlInSafeMode'];
            $permitRawHtml = !$this->safeMode || $allowRawHtmlInSafeMode;
        }

        $hasContent = isset($text) || isset($Element['element']) || isset($Element['elements']);

        if ($hasContent)
        {
            $markup .= $hasName ? '>' : '';

            if (isset($Element['elements']))
            {
                $markup .= $this->elements($Element['elements']);
            }
            elseif (isset($Element['element']))
            {
                $markup .= $this->element($Element['element']);
            }
            else
            {
                if (!$permitRawHtml)
                {
                    $markup .= self::escape((string) $text, true);
                }
                else
                {
                    $markup .= $text;
                }
            }

            $markup .= $hasName ? '</' . $Element['name'] . '>' : '';
        }
        elseif ($hasName)
        {
            $markup .= ' />';
        }

        return $markup;
    }

    protected function elements(array $Elements)
    {
        $markup = '';

        $autoBreak = true;

        foreach ($Elements as $Element)
        {
            if (empty($Element))
            {
                continue;
            }

            $autoBreakNext = (isset($Element['autobreak'])
                ? $Element['autobreak'] : isset($Element['name'])
            );
            // (autobreak === false) covers both sides of an element
            $autoBreak = !$autoBreak ? $autoBreak : $autoBreakNext;

            $markup .= ($autoBreak ? "\n" : '') . $this->element($Element);
            $autoBreak = $autoBreakNext;
        }

        $markup .= $autoBreak ? "\n" : '';

        return $markup;
    }

    # ~

    protected function li($lines)
    {
        $Elements = $this->linesElements($lines);

        if ( ! in_array('', $lines)
            and isset($Elements[0]) and isset($Elements[0]['name'])
            and $Elements[0]['name'] === 'p'
        ) {
            unset($Elements[0]['name']);
        }

        return $Elements;
    }

    #
    # AST Convenience
    #

    /**
     * Replace occurrences $regexp with $Elements in $text. Return an array of
     * elements representing the replacement.
     */
    protected static function pregReplaceElements($regexp, $Elements, $text)
    {
        $newElements = array();

        while (preg_match($regexp, $text, $matches, PREG_OFFSET_CAPTURE))
        {
            $offset = $matches[0][1];
            $before = substr($text, 0, $offset);
            $after = substr($text, $offset + strlen($matches[0][0]));

            $newElements[] = array('text' => $before);

            foreach ($Elements as $Element)
            {
                $newElements[] = $Element;
            }

            $text = $after;
        }

        $newElements[] = array('text' => $text);

        return $newElements;
    }

    #
    # Deprecated Methods
    #

    public static function parse($text)
    {
        $parsedown = new self();

        return $parsedown->text($text);
    }

    protected function sanitiseElement(array $Element)
    {
        static $goodAttribute = '/^[a-zA-Z0-9][a-zA-Z0-9-_]*+$/';
        static $safeUrlNameToAtt  = array(
            'a'   => 'href',
            'img' => 'src',
        );

        if ( ! isset($Element['name']))
        {
            unset($Element['attributes']);
            return $Element;
        }

        if (isset($safeUrlNameToAtt[$Element['name']]))
        {
            $Element = $this->filterUnsafeUrlInAttribute($Element, $safeUrlNameToAtt[$Element['name']]);
        }

        if ( ! empty($Element['attributes']))
        {
            foreach ($Element['attributes'] as $att => $val)
            {
                # filter out badly parsed attribute
                if ( ! preg_match($goodAttribute, $att))
                {
                    unset($Element['attributes'][$att]);
                }
                # dump onevent attribute
                elseif (self::striAtStart($att, 'on'))
                {
                    unset($Element['attributes'][$att]);
                }
            }
        }

        return $Element;
    }

    protected function filterUnsafeUrlInAttribute(array $Element, $attribute)
    {
        foreach ($this->safeLinksWhitelist as $scheme)
        {
            if (self::striAtStart($Element['attributes'][$attribute], $scheme))
            {
                return $Element;
            }
        }

        $Element['attributes'][$attribute] = str_replace(':', '%3A', $Element['attributes'][$attribute]);

        return $Element;
    }

    #
    # Static Methods
    #

    protected static function escape(string $text, bool $allowQuotes = false)
    {
        return \htmlspecialchars($text, $allowQuotes ? ENT_NOQUOTES : ENT_QUOTES, 'UTF-8');
    }

    protected static function striAtStart($string, $needle)
    {
        $len = strlen($needle);

        if ($len > strlen($string))
        {
            return false;
        }
        else
        {
            return strtolower(substr($string, 0, $len)) === strtolower($needle);
        }
    }

    static function instance($name = 'default')
    {
        if (isset(self::$instances[$name]))
        {
            return self::$instances[$name];
        }

        $instance = new static();

        self::$instances[$name] = $instance;

        return $instance;
    }

    private static $instances = array();

    #
    # Fields
    #

    protected $DefinitionData;

    public const ID_ATTRIBUTE_DEFAULT = 'toc';
    protected $tagToc = '[toc]';

    protected $contentsListArray = [];
    protected $contentsListString = '';
    protected $firstHeadLevel = 0;

    protected $isBlacklistInitialized = false;
    protected $anchorDuplicates = [];

    #
    # Read-Only

    protected $specialCharacters = array(
        '\\', '`', '*', '_', '{', '}', '[', ']', '(', ')', '>', '#', '+', '-', '.', '!', '|', '?', '"', "'", '<',
    );

    protected $StrongRegex = array(
        '*' => '/^[*]{2}((?:\\\\\*|[^*]|[*][^*]*+[*])+?)[*]{2}(?![*])/s',
    );

    protected $UnderlineRegex = array(
        '_' => '/^__((?:\\\\_|[^_]|_[^_]*+_)+?)__(?!_)/us',
    );

    protected $EmRegex = array(
        '*' => '/^[*]((?:\\\\\*|[^*]|[*][*][^*]+?[*][*])+?)[*](?![*])/s',
        '_' => '/^_((?:\\\\_|[^_]|__[^_]*__)+?)_(?!_)\b/us',
    );

    protected $regexHtmlAttribute = '[a-zA-Z_:][\w:.-]*+(?:\s*+=\s*+(?:[^"\'=<>`\s]+|"[^"]*+"|\'[^\']*+\'))?+';

    protected $voidElements = array(
        'area', 'base', 'br', 'col', 'command', 'embed', 'hr', 'img', 'input', 'link', 'meta', 'param', 'source',
    );

    protected $textLevelElements = array(
        'a', 'br', 'bdo', 'abbr', 'blink', 'nextid', 'acronym', 'basefont',
        'b', 'em', 'big', 'cite', 'small', 'spacer', 'listing',
        'i', 'rp', 'del', 'code',          'strike', 'marquee',
        'q', 'rt', 'ins', 'font',          'strong',
        's', 'tt', 'kbd', 'mark',
        'u', 'xm', 'sub', 'nobr',
                   'sup', 'ruby',
                   'var', 'span',
                   'wbr', 'time',
    );
}
