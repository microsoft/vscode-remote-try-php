<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Util;

if (!defined('ABSPATH')) exit;


class FreeDomains {

  /* https://github.com/mailcheck/mailcheck/wiki/List-of-Popular-Domains */
  const FREE_DOMAINS = [
    /* Default domains included */
    'aol.com', 'att.net', 'comcast.net', 'facebook.com', 'gmail.com', 'gmx.com', 'googlemail.com',
    'google.com', 'hotmail.com', 'hotmail.co.uk', 'mac.com', 'me.com', 'mail.com', 'msn.com',
    'live.com', 'sbcglobal.net', 'verizon.net', 'yahoo.com', 'yahoo.co.uk',

    /* Other global domains */
    'email.com', 'fastmail.fm', 'games.com' /* AOL */, 'gmx.net', 'hush.com', 'hushmail.com', 'icloud.com',
    'iname.com', 'inbox.com', 'lavabit.com', 'love.com' /* AOL */, 'outlook.com', 'pobox.com', 'protonmail.ch', 'protonmail.com', 'tutanota.de', 'tutanota.com', 'tutamail.com', 'tuta.io',
    'keemail.me', 'rocketmail.com' /* Yahoo */, 'safe-mail.net', 'wow.com' /* AOL */, 'ygm.com' /* AOL */,
    'ymail.com' /* Yahoo */, 'zoho.com', 'yandex.com',

    /* United States ISP domains */
    'bellsouth.net', 'charter.net', 'cox.net', 'earthlink.net', 'juno.com',

    /* British ISP domains */
    'btinternet.com', 'virginmedia.com', 'blueyonder.co.uk', 'live.co.uk',
    'ntlworld.com', 'orange.net', 'sky.com', 'talktalk.co.uk', 'tiscali.co.uk',
    'virgin.net', 'bt.com',

    /* Domains used in Asia */
    'sina.com', 'sina.cn', 'qq.com', 'naver.com', 'hanmail.net', 'daum.net', 'nate.com', 'yahoo.co.jp', 'yahoo.co.kr', 'yahoo.co.id', 'yahoo.co.in', 'yahoo.com.sg', 'yahoo.com.ph', '163.com', 'yeah.net', '126.com', '21cn.com', 'aliyun.com', 'foxmail.com',

    /* French ISP domains */
    'hotmail.fr', 'live.fr', 'laposte.net', 'yahoo.fr', 'wanadoo.fr', 'orange.fr', 'gmx.fr', 'sfr.fr', 'neuf.fr', 'free.fr',

    /* German ISP domains */
    'gmx.de', 'hotmail.de', 'live.de', 'online.de', 't-online.de' /* T-Mobile */, 'web.de', 'yahoo.de',

    /* Italian ISP domains */
    'libero.it', 'virgilio.it', 'hotmail.it', 'aol.it', 'tiscali.it', 'alice.it', 'live.it', 'yahoo.it', 'email.it', 'tin.it', 'poste.it', 'teletu.it',

    /* Russian ISP domains */
    'bk.ru', 'inbox.ru', 'list.ru', 'mail.ru', 'rambler.ru', 'yandex.by', 'yandex.com', 'yandex.kz', 'yandex.ru', 'yandex.ua', 'ya.ru',

    /* Belgian ISP domains */
    'hotmail.be', 'live.be', 'skynet.be', 'voo.be', 'tvcablenet.be', 'telenet.be',

    /* Argentinian ISP domains */
    'hotmail.com.ar', 'live.com.ar', 'yahoo.com.ar', 'fibertel.com.ar', 'speedy.com.ar', 'arnet.com.ar',

    /* Domains used in Mexico */
    'yahoo.com.mx', 'live.com.mx', 'hotmail.es', 'hotmail.com.mx', 'prodigy.net.mx',

    /* Domains used in Canada */
    'yahoo.ca', 'hotmail.ca', 'bell.net', 'shaw.ca', 'sympatico.ca', 'rogers.com',

    /* Domains used in Brazil */
    'yahoo.com.br', 'hotmail.com.br', 'outlook.com.br', 'uol.com.br', 'bol.com.br', 'terra.com.br', 'ig.com.br', 'r7.com', 'zipmail.com.br', 'globo.com', 'globomail.com', 'oi.com.br',
  ];

  public function isEmailOnFreeDomain($email) {
    $emailParts = explode('@', $email);
    $domain = end($emailParts);
    return in_array($domain, self::FREE_DOMAINS);
  }
}
