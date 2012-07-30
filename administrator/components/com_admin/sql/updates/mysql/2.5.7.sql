# Placeholder file for database changes for version 2.5.7

# Insert the update server for Accredited translations if not exist
INSERT IGNORE INTO
    `#__update_sites`
VALUES
    (10000,
    'Accredited Joomla! Translations',
     'collection',
     'http://update.joomla.org/language/translationlist.xml',
     1,
     0);

# Link english lang and the Accredited translations update site for JUpdater needs
INSERT IGNORE INTO
    `#__update_sites_extensions`
VALUES
    (10000,
    600);