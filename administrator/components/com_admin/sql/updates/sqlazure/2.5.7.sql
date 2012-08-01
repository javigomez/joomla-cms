SET IDENTITY_INSERT #__update_sites  ON;

INSERT INTO #__update_sites (update_site_id, name, type, location, enabled, last_check_timestamp) SELECT 10000, 'Accredited Joomla! Translations', 'collection', 'http://update.joomla.org/language/translationlist.xml', 1, 0;

SET IDENTITY_INSERT #__update_sites  OFF;


SET IDENTITY_INSERT #__update_sites_extensions  ON;

INSERT INTO #__update_sites_extensions (update_site_id, extension_id) SELECT 10000, 600;

SET IDENTITY_INSERT #__update_sites_extensions  OFF;
