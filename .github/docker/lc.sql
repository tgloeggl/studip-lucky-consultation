SET FOREIGN_KEY_CHECKS=0;

-- activate plugin in course
REPLACE INTO tools_activated
    (range_id, range_type, plugin_id, position, metadata, mkdate, chdate) VALUES
('a07535cf2f8a72df33c12ddfa4b53dde', 'course', 29, 11, '[]', 1699267230, 1699267230);

REPLACE INTO `luckyconsultation_pools` (`id`, `course_id`, `name`, `date`, `lots_drawn`, `template`, `mkdate`, `chdate`) VALUES
(1,	'a07535cf2f8a72df33c12ddfa4b53dde',	'PP',	'2025-07-01 16:00:00',	0,	'PP',	'2025-07-01 17:30:21',	'2025-07-01 17:30:21'),
(2,	'a07535cf2f8a72df33c12ddfa4b53dde',	'KJP',	'2025-07-01 16:00:00',	0,	'KJP',	'2025-07-06 11:13:46',	'2025-07-06 11:13:46');

REPLACE INTO `luckyconsultation_dates` (`id`, `course_id`, `user_id`, `description`, `start`, `pool`, `history`, `mkdate`, `chdate`, `fs_start`, `fs_slot`, `fs_room`, `ko_date`, `ko_room`, `therapist_id`) VALUES
(1, 'a07535cf2f8a72df33c12ddfa4b53dde', NULL, 'Testaccount Dozent', DATE_ADD(CURDATE(), INTERVAL 14 DAY), 1, '{}', NOW(), NOW(), 'fsstart', 'fsslot', 'fsraum', 'kodatum', 'koraum', '205f3efb7997a0fc9755da2b535038da'),
(2, 'a07535cf2f8a72df33c12ddfa4b53dde', NULL, 'Testaccount Dozent', DATE_ADD(CURDATE(), INTERVAL 15 DAY), 2, '{}', NOW(), NOW(), 'fsstart', 'fsslot', 'fsraum', 'kodatum', 'koraum', '205f3efb7997a0fc9755da2b535038da'),
(3, 'a07535cf2f8a72df33c12ddfa4b53dde', NULL, 'Testaccount Dozent', DATE_ADD(CURDATE(), INTERVAL 16 DAY), 1, '{}', NOW(), NOW(), 'fsstart', 'fsslot', 'fsraum', 'kodatum', 'koraum', '205f3efb7997a0fc9755da2b535038da'),
(4, 'a07535cf2f8a72df33c12ddfa4b53dde', NULL, 'Testaccount Dozent', DATE_ADD(CURDATE(), INTERVAL 17 DAY), 1, '{}', NOW(), NOW(), 'fsstart', 'fsslot', 'fsraum', 'kodatum', 'koraum', '205f3efb7997a0fc9755da2b535038da'),
(5, 'a07535cf2f8a72df33c12ddfa4b53dde', NULL, 'Testaccount Dozent', DATE_ADD(CURDATE(), INTERVAL 18 DAY), 2, '{}', NOW(), NOW(), 'fsstart', 'fsslot', 'fsraum', 'kodatum', 'koraum', '205f3efb7997a0fc9755da2b535038da'),
(6, 'a07535cf2f8a72df33c12ddfa4b53dde', NULL, 'Testaccount Dozent', DATE_ADD(CURDATE(), INTERVAL 19 DAY), 2, '{}', NOW(), NOW(), 'fsstart', 'fsslot', 'fsraum', 'kodatum', 'koraum', '205f3efb7997a0fc9755da2b535038da');

REPLACE INTO `luckyconsultation_waitinglist` (`dates_id`, `user_id`, `mkdate`, `chdate`) VALUES
(1,	'e7a0a84b161f3e8c09b4a0a2e8a58147',	'2025-07-06 11:13:32',	'2025-07-06 11:13:32'),
(2,	'e7a0a84b161f3e8c09b4a0a2e8a58147',	'2025-07-01 17:32:26',	'2025-07-01 17:32:26'),
(3,	'e7a0a84b161f3e8c09b4a0a2e8a58147',	'2025-07-06 11:14:00',	'2025-07-06 11:14:00'),
(5,	'e7a0a84b161f3e8c09b4a0a2e8a58147',	'2025-07-06 11:14:30',	'2025-07-06 11:14:30');

SET FOREIGN_KEY_CHECKS=1;