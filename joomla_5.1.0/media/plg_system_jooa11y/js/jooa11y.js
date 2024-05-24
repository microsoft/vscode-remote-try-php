import { Lang, Sa11y } from 'sa11y';
import Sa11yLang from 'sa11y-lang';

// eslint-disable-next-line import/no-unresolved
Lang.addI18n(Sa11yLang.strings);
window.addEventListener('load', () => {
  // eslint-disable-next-line no-new
  new Sa11y(Joomla.getOptions('jooa11yOptions', {}));
});
