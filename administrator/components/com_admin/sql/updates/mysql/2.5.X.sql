# Insert the update server for Accredited translations if not exist
INSERT INTO
	`#__update_sites` ( update_site_id, name, type, location, enabled, last_check_timestamp )
SELECT 
	null, 
	'Accredited Joomla! Translations', 
	'collection', 
	'http://update.joomla.org/language/translationlist.xml', 
	1, 
	0
FROM `#__update_sites`
WHERE NOT EXISTS (
	SELECT 
		* 
	FROM 
		`#__update_sites` 
	WHERE 
		location = 'http://update.joomla.org/language/translationlist.xml');
# Link english lang and the Accredited translations update site for JUpdater needs
# TODO insert not strict
INSERT IGNORE INTO
	`#__update_sites_extensions` 
VALUES
	(
		(
			SELECT 
				update_site_id 
			FROM 
				`#__update_sites` 
			WHERE 
				location = 'http://update.joomla.org/language/translationlist.xml'
		)
		, 600
	);