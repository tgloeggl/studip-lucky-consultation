SET FOREIGN_KEY_CHECKS=0;

-- activate plugin in course
REPLACE INTO tools_activated
    (range_id, range_type, plugin_id, position, metadata, mkdate, chdate) VALUES
('a07535cf2f8a72df33c12ddfa4b53dde', 'course', 29, 11, '[]', 1699267230, 1699267230);

INSERT INTO `luckyconsultation_pools` (`id`, `course_id`, `name`, `date`, `lots_drawn`, `template`, `mkdate`, `chdate`) VALUES
(1,	'a07535cf2f8a72df33c12ddfa4b53dde',	'PP',	'2025-07-01 16:00:00',	0,	'PP',	'2025-07-01 17:30:21',	'2025-07-01 17:30:21'),
(2,	'a07535cf2f8a72df33c12ddfa4b53dde',	'KJP',	'2025-07-01 16:00:00',	0,	'KJP',	'2025-07-06 11:13:46',	'2025-07-06 11:13:46');

INSERT INTO `luckyconsultation_dates` (`id`, `course_id`, `user_id`, `description`, `start`, `pool`, `history`, `mkdate`, `chdate`, `fs_start`, `fs_slot`, `fs_room`, `ko_date`, `ko_room`, `therapist_id`) VALUES
(1,	'a07535cf2f8a72df33c12ddfa4b53dde',	NULL,	'Testaccount Dozent',	'2025-07-07 14:00:00',	1,	'{\"17:50:14_01.07.2025\":[\"Testaccount Autor (test_autor)\"],\"17:50:59_01.07.2025\":[\"Testaccount Autor (test_autor)\"],\"17:51:15_01.07.2025\":[\"Testaccount Autor (test_autor)\"],\"17:51:23_01.07.2025\":[\"Testaccount Autor (test_autor)\"],\"17:52:55_01.07.2025\":[\"Testaccount Autor (test_autor)\"],\"18:00:02_01.07.2025\":[\"Testaccount Autor (test_autor)\"],\"18:00:47_01.07.2025\":[\"Testaccount Autor (test_autor)\"],\"18:01:53_01.07.2025\":[\"Testaccount Autor (test_autor)\"]}',	'2025-07-06 11:12:50',	'2025-07-06 11:12:50',	'fsstart',	'fsslot',	'fsraum',	'kodatum',	'koraum',	'205f3efb7997a0fc9755da2b535038da'),
(2,	'a07535cf2f8a72df33c12ddfa4b53dde',	NULL,	'Testaccount Dozent',	'2025-07-07 14:00:00',	2,	'{\"17:50:14_01.07.2025\":[\"Testaccount Autor (test_autor)\"],\"17:50:59_01.07.2025\":[\"Testaccount Autor (test_autor)\"],\"17:51:15_01.07.2025\":[\"Testaccount Autor (test_autor)\"],\"17:51:23_01.07.2025\":[\"Testaccount Autor (test_autor)\"],\"17:52:55_01.07.2025\":[\"Testaccount Autor (test_autor)\"],\"18:00:02_01.07.2025\":[\"Testaccount Autor (test_autor)\"],\"18:00:47_01.07.2025\":[\"Testaccount Autor (test_autor)\"],\"18:01:53_01.07.2025\":[\"Testaccount Autor (test_autor)\"]}',	'2025-07-01 18:01:53',	'2025-07-01 18:01:53',	'fsstart',	'fsslot',	'fsraum',	'kodatum',	'koraum',	'205f3efb7997a0fc9755da2b535038da');

INSERT INTO `luckyconsultation_waitinglist` (`dates_id`, `user_id`, `mkdate`, `chdate`) VALUES
(1,	'e7a0a84b161f3e8c09b4a0a2e8a58147',	'2025-07-06 11:13:32',	'2025-07-06 11:13:32'),
(2,	'e7a0a84b161f3e8c09b4a0a2e8a58147',	'2025-07-01 17:32:26',	'2025-07-01 17:32:26');

SET FOREIGN_KEY_CHECKS=1;
