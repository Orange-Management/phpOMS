<?php
/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package   phpOMS\Localization
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace phpOMS\Localization;

/**
 * ISO 4217 country -> currency trait.
 *
 * @package phpOMS\Localization
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
trait ISO4217Trait
{
    /**
     * Get currency from country.
     *
     * @param string $country Country 2 code
     *
     * @return array
     *
     * @since 1.0.0
     */
    public static function currencyFromCountry(string $country) : string
    {
        switch (\strtoupper($country)) {
            case ISO3166TwoEnum::_AFG:
                return self::_USD;
            case ISO3166TwoEnum::_ALA:
                return self::_EUR;
            case ISO3166TwoEnum::_ALB:
                return self::_EUR;
            case ISO3166TwoEnum::_DZA:
                return self::_DZD;
            case ISO3166TwoEnum::_ASM:
                return self::_USD;
            case ISO3166TwoEnum::_AND:
                return self::_EUR;
            case ISO3166TwoEnum::_AGO:
                return self::_AOA;
            case ISO3166TwoEnum::_AIA:
                return self::_XCD;
            case ISO3166TwoEnum::_ATG:
                return self::_XCD;
            case ISO3166TwoEnum::_ARG:
                return self::_ARS;
            case ISO3166TwoEnum::_ARM:
                return self::_AMD;
            case ISO3166TwoEnum::_ABW:
                return self::_AWG;
            case ISO3166TwoEnum::_AUS:
                return self::_AUD;
            case ISO3166TwoEnum::_AUT:
                return self::_EUR;
            case ISO3166TwoEnum::_AZE:
                return self::_AZM;
            case ISO3166TwoEnum::_PRT:
                return self::_EUR;
            case ISO3166TwoEnum::_BHS:
                return self::_BSD;
            case ISO3166TwoEnum::_BHR:
                return self::_BHD;
            case ISO3166TwoEnum::_BGD:
                return self::_BDT;
            case ISO3166TwoEnum::_BRB:
                return self::_BBD;
            case ISO3166TwoEnum::_BLR:
                return self::_BYR;
            case ISO3166TwoEnum::_BEL:
                return self::_EUR;
            case ISO3166TwoEnum::_BLZ:
                return self::_BZD;
            case ISO3166TwoEnum::_BEN:
                return self::_XOF;
            case ISO3166TwoEnum::_BMU:
                return self::_BMD;
            case ISO3166TwoEnum::_BTN:
                return self::_BTN;
            case ISO3166TwoEnum::_BOL:
                return self::_BOB;
            case ISO3166TwoEnum::_BES:
                return self::_ANG;
            case ISO3166TwoEnum::_BIH:
                return self::_BAM;
            case ISO3166TwoEnum::_BWA:
                return self::_BWP;
            case ISO3166TwoEnum::_BRA:
                return self::_BRL;
            case ISO3166TwoEnum::_VGB:
                return self::_USD;
            case ISO3166TwoEnum::_BRN:
                return self::_BND;
            case ISO3166TwoEnum::_BGR:
                return self::_EUR;
            case ISO3166TwoEnum::_BFA:
                return self::_XOF;
            case ISO3166TwoEnum::_BDI:
                return self::_BIF;
            case ISO3166TwoEnum::_KHM:
                return self::_KHR;
            case ISO3166TwoEnum::_CMR:
                return self::_XAF;
            case ISO3166TwoEnum::_CAN:
                return self::_CAD;
            case ISO3166TwoEnum::_ESP:
                return self::_EUR;
            case ISO3166TwoEnum::_CPV:
                return self::_CVE;
            case ISO3166TwoEnum::_CYM:
                return self::_KYD;
            case ISO3166TwoEnum::_CAF:
                return self::_XAF;
            case ISO3166TwoEnum::_TCD:
                return self::_XAF;
            case ISO3166TwoEnum::_CHL:
                return self::_CLP;
            case ISO3166TwoEnum::_CHN:
                return self::_CNY;
            case ISO3166TwoEnum::_COL:
                return self::_COP;
            case ISO3166TwoEnum::_COM:
                return self::_USD;
            case ISO3166TwoEnum::_COG:
                return self::_XAF;
            case ISO3166TwoEnum::_COD:
                return self::_CDF;
            case ISO3166TwoEnum::_COK:
                return self::_NZD;
            case ISO3166TwoEnum::_CRI:
                return self::_CRC;
            case ISO3166TwoEnum::_HRV:
                return self::_EUR;
            case ISO3166TwoEnum::_CUW:
                return self::_USD;
            case ISO3166TwoEnum::_CYP:
                return self::_EUR;
            case ISO3166TwoEnum::_CZE:
                return self::_CZK;
            case ISO3166TwoEnum::_DNK:
                return self::_DKK;
            case ISO3166TwoEnum::_DJI:
                return self::_DJF;
            case ISO3166TwoEnum::_DMA:
                return self::_XCD;
            case ISO3166TwoEnum::_DOM:
                return self::_DOP;
            case ISO3166TwoEnum::_TLS:
                return self::_USD;
            case ISO3166TwoEnum::_ECU:
                return self::_USD;
            case ISO3166TwoEnum::_EGY:
                return self::_EGP;
            case ISO3166TwoEnum::_SLV:
                return self::_USD;
            case ISO3166TwoEnum::_GBR:
                return self::_GBP;
            case ISO3166TwoEnum::_GNQ:
                return self::_XAF;
            case ISO3166TwoEnum::_ERI:
                return self::_ERN;
            case ISO3166TwoEnum::_EST:
                return self::_EUR;
            case ISO3166TwoEnum::_ETH:
                return self::_ETB;
            case ISO3166TwoEnum::_FRO:
                return self::_DKK;
            case ISO3166TwoEnum::_FJI:
                return self::_FJD;
            case ISO3166TwoEnum::_FIN:
                return self::_EUR;
            case ISO3166TwoEnum::_FRA:
                return self::_EUR;
            case ISO3166TwoEnum::_GUF:
                return self::_EUR;
            case ISO3166TwoEnum::_PYF:
                return self::_XPF;
            case ISO3166TwoEnum::_GAB:
                return self::_XAF;
            case ISO3166TwoEnum::_GMB:
                return self::_GMD;
            case ISO3166TwoEnum::_GEO:
                return self::_GEL;
            case ISO3166TwoEnum::_DEU:
                return self::_EUR;
            case ISO3166TwoEnum::_GHA:
                return self::_GHS;
            case ISO3166TwoEnum::_GIB:
                return self::_GIP;
            case ISO3166TwoEnum::_GRC:
                return self::_EUR;
            case ISO3166TwoEnum::_GRL:
                return self::_DKK;
            case ISO3166TwoEnum::_GRD:
                return self::_XCD;
            case ISO3166TwoEnum::_GLP:
                return self::_EUR;
            case ISO3166TwoEnum::_GUM:
                return self::_USD;
            case ISO3166TwoEnum::_GTM:
                return self::_GTQ;
            case ISO3166TwoEnum::_GGY:
                return self::_GBP;
            case ISO3166TwoEnum::_GIN:
                return self::_GNF;
            case ISO3166TwoEnum::_GNB:
                return self::_XOF;
            case ISO3166TwoEnum::_GUY:
                return self::_GYD;
            case ISO3166TwoEnum::_HTI:
                return self::_HTG;
            case ISO3166TwoEnum::_NLD:
                return self::_EUR;
            case ISO3166TwoEnum::_HND:
                return self::_HNL;
            case ISO3166TwoEnum::_HKG:
                return self::_HKD;
            case ISO3166TwoEnum::_HUN:
                return self::_HUF;
            case ISO3166TwoEnum::_ISL:
                return self::_ISK;
            case ISO3166TwoEnum::_IND:
                return self::_INR;
            case ISO3166TwoEnum::_IDN:
                return self::_IDR;
            case ISO3166TwoEnum::_IRQ:
                return self::_NID;
            case ISO3166TwoEnum::_IRL:
                return self::_EUR;
            case ISO3166TwoEnum::_ISR:
                return self::_ILS;
            case ISO3166TwoEnum::_ITA:
                return self::_EUR;
            case ISO3166TwoEnum::_CIV:
                return self::_XOF;
            case ISO3166TwoEnum::_JAM:
                return self::_JMD;
            case ISO3166TwoEnum::_JPN:
                return self::_JPY;
            case ISO3166TwoEnum::_JEY:
                return self::_GBP;
            case ISO3166TwoEnum::_JOR:
                return self::_JOD;
            case ISO3166TwoEnum::_KAZ:
                return self::_KZT;
            case ISO3166TwoEnum::_KEN:
                return self::_KES;
            case ISO3166TwoEnum::_KIR:
                return self::_AUD;
            case ISO3166TwoEnum::_KOR:
                return self::_KRW;
            case ISO3166TwoEnum::_FSM:
                return self::_USD;
            case ISO3166TwoEnum::_KWT:
                return self::_KWD;
            case ISO3166TwoEnum::_KGZ:
                return self::_KGS;
            case ISO3166TwoEnum::_LAO:
                return self::_LAK;
            case ISO3166TwoEnum::_LVA:
                return self::_EUR;
            case ISO3166TwoEnum::_LBN:
                return self::_LBP;
            case ISO3166TwoEnum::_LSO:
                return self::_LSL;
            case ISO3166TwoEnum::_LBR:
                return self::_LRD;
            case ISO3166TwoEnum::_LBY:
                return self::_LYD;
            case ISO3166TwoEnum::_LIE:
                return self::_CHF;
            case ISO3166TwoEnum::_LTU:
                return self::_EUR;
            case ISO3166TwoEnum::_LUX:
                return self::_EUR;
            case ISO3166TwoEnum::_MAC:
                return self::_MOP;
            case ISO3166TwoEnum::_MKD:
                return self::_EUR;
            case ISO3166TwoEnum::_MDG:
                return self::_MGA;
            case ISO3166TwoEnum::_MWI:
                return self::_MWK;
            case ISO3166TwoEnum::_MYS:
                return self::_MYR;
            case ISO3166TwoEnum::_MDV:
                return self::_MVR;
            case ISO3166TwoEnum::_MLI:
                return self::_XOF;
            case ISO3166TwoEnum::_MLT:
                return self::_EUR;
            case ISO3166TwoEnum::_MHL:
                return self::_USD;
            case ISO3166TwoEnum::_MTQ:
                return self::_EUR;
            case ISO3166TwoEnum::_MRT:
                return self::_MRO;
            case ISO3166TwoEnum::_MUS:
                return self::_MUR;
            case ISO3166TwoEnum::_MYT:
                return self::_EUR;
            case ISO3166TwoEnum::_MEX:
                return self::_MXN;
            case ISO3166TwoEnum::_MDA:
                return self::_MDL;
            case ISO3166TwoEnum::_MCO:
                return self::_EUR;
            case ISO3166TwoEnum::_MNG:
                return self::_MNT;
            case ISO3166TwoEnum::_MNE:
                return self::_EUR;
            case ISO3166TwoEnum::_MSR:
                return self::_XCD;
            case ISO3166TwoEnum::_MAR:
                return self::_MAD;
            case ISO3166TwoEnum::_MOZ:
                return self::_MZM;
            case ISO3166TwoEnum::_NAM:
                return self::_NAD;
            case ISO3166TwoEnum::_NPL:
                return self::_NPR;
            case ISO3166TwoEnum::_NCL:
                return self::_XPF;
            case ISO3166TwoEnum::_NZL:
                return self::_NZD;
            case ISO3166TwoEnum::_NIC:
                return self::_NIO;
            case ISO3166TwoEnum::_NER:
                return self::_XOF;
            case ISO3166TwoEnum::_NGA:
                return self::_NGN;
            case ISO3166TwoEnum::_NFK:
                return self::_AUD;
            case ISO3166TwoEnum::_MNP:
                return self::_USD;
            case ISO3166TwoEnum::_NOR:
                return self::_NOK;
            case ISO3166TwoEnum::_OMN:
                return self::_OMR;
            case ISO3166TwoEnum::_PAK:
                return self::_PKR;
            case ISO3166TwoEnum::_PLW:
                return self::_USD;
            case ISO3166TwoEnum::_PAN:
                return self::_PAB;
            case ISO3166TwoEnum::_PNG:
                return self::_PGK;
            case ISO3166TwoEnum::_PRY:
                return self::_PYG;
            case ISO3166TwoEnum::_PER:
                return self::_PEN;
            case ISO3166TwoEnum::_PHL:
                return self::_PHP;
            case ISO3166TwoEnum::_POL:
                return self::_PLN;
            case ISO3166TwoEnum::_PRI:
                return self::_USD;
            case ISO3166TwoEnum::_QAT:
                return self::_QAR;
            case ISO3166TwoEnum::_REU:
                return self::_EUR;
            case ISO3166TwoEnum::_ROU:
                return self::_ROL;
            case ISO3166TwoEnum::_RUS:
                return self::_RUB;
            case ISO3166TwoEnum::_RWA:
                return self::_RWF;
            case ISO3166TwoEnum::_WSM:
                return self::_WST;
            case ISO3166TwoEnum::_SMR:
                return self::_EUR;
            case ISO3166TwoEnum::_STP:
                return self::_STD;
            case ISO3166TwoEnum::_SAU:
                return self::_SAR;
            case ISO3166TwoEnum::_SEN:
                return self::_XOF;
            case ISO3166TwoEnum::_SRB:
                return self::_EUR;
            case ISO3166TwoEnum::_SYC:
                return self::_SCR;
            case ISO3166TwoEnum::_SLE:
                return self::_SLL;
            case ISO3166TwoEnum::_SGP:
                return self::_SGD;
            case ISO3166TwoEnum::_SVK:
                return self::_EUR;
            case ISO3166TwoEnum::_SVN:
                return self::_EUR;
            case ISO3166TwoEnum::_SLB:
                return self::_SBD;
            case ISO3166TwoEnum::_ZAF:
                return self::_ZAR;
            case ISO3166TwoEnum::_LKA:
                return self::_LKR;
            case ISO3166TwoEnum::_BLM:
                return self::_EUR;
            case ISO3166TwoEnum::_KNA:
                return self::_XCD;
            case ISO3166TwoEnum::_VIR:
                return self::_USD;
            case ISO3166TwoEnum::_LCA:
                return self::_XCD;
            case ISO3166TwoEnum::_SXM:
                return self::_USD;
            case ISO3166TwoEnum::_VCT:
                return self::_XCD;
            case ISO3166TwoEnum::_SUR:
                return self::_SRG;
            case ISO3166TwoEnum::_SWZ:
                return self::_SZL;
            case ISO3166TwoEnum::_SWE:
                return self::_SEK;
            case ISO3166TwoEnum::_CHE:
                return self::_CHF;
            case ISO3166TwoEnum::_TWN:
                return self::_TWD;
            case ISO3166TwoEnum::_TJK:
                return self::_TJS;
            case ISO3166TwoEnum::_TZA:
                return self::_TZS;
            case ISO3166TwoEnum::_THA:
                return self::_THB;
            case ISO3166TwoEnum::_TGO:
                return self::_XOF;
            case ISO3166TwoEnum::_TON:
                return self::_TOP;
            case ISO3166TwoEnum::_TTO:
                return self::_TTD;
            case ISO3166TwoEnum::_TUN:
                return self::_TND;
            case ISO3166TwoEnum::_TUR:
                return self::_TRY;
            case ISO3166TwoEnum::_TKM:
                return self::_TMT;
            case ISO3166TwoEnum::_TCA:
                return self::_USD;
            case ISO3166TwoEnum::_TUV:
                return self::_AUD;
            case ISO3166TwoEnum::_UGA:
                return self::_UGX;
            case ISO3166TwoEnum::_UKR:
                return self::_UAH;
            case ISO3166TwoEnum::_ARE:
                return self::_AED;
            case ISO3166TwoEnum::_USA:
                return self::_USD;
            case ISO3166TwoEnum::_URY:
                return self::_UYU;
            case ISO3166TwoEnum::_UZB:
                return self::_UZS;
            case ISO3166TwoEnum::_VUT:
                return self::_VUV;
            case ISO3166TwoEnum::_VAT:
                return self::_EUR;
            case ISO3166TwoEnum::_VEN:
                return self::_VEB;
            case ISO3166TwoEnum::_VNM:
                return self::_VND;
            case ISO3166TwoEnum::_WLF:
                return self::_XPF;
            case ISO3166TwoEnum::_YEM:
                return self::_YER;
            case ISO3166TwoEnum::_ZMB:
                return self::_ZMK;
            case ISO3166TwoEnum::_ZWE:
                return self::_ZWD;
            default:
                return '';
        }
    }
}