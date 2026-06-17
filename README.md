# LuckyConsultation

## Deutsch

LuckyConsultation ist ein Stud.IP-Plugin zur losbasierten Vergabe von Sprechstunden- bzw. Beratungsterminen in Kursen. Lehrende oder andere berechtigte Kursrollen legen Termingruppen an, Studierende tragen sich auf Loslisten ein, und ein Cronjob lost zu festgelegten Zeitpunkten die Zuweisungen aus.

Das Plugin ist besonders für Situationen gedacht, in denen mehrere gleichartige Termine fair verteilt werden sollen und eine einfache Reihenfolge nach Anmeldung nicht ausreicht.

### Funktionen

- Verwaltung von Lospools mit eigenem Loszeitpunkt.
- Anlage, Bearbeitung und Freigabe einzelner Termine innerhalb eines Pools.
- Trennung zwischen Entwürfen und freigegebenen Terminen.
- Eintragung und Austragung von Studierenden auf Loslisten.
- Anzeige bereits zugeloster eigener Termine für Studierende.
- Anzeige von Auslastung, Loslisten und Loshistorie für berechtigte Kursrollen.
- Automatische Auslosung über einen Stud.IP-Cronjob.
- Vermeidung zeitnaher Mehrfachzuweisungen durch Prüfung auf bereits zugeloste Termine im Zeitfenster von einer Stunde vor bis einer Stunde nach dem jeweiligen Termin.
- Benachrichtigung der zugelosten Person und der zugeordneten betreuenden Person per Stud.IP-Nachricht.
- Globale Mailvorlagen für die Nachrichtentypen `PP` und `KJP` mit Platzhaltern.
- Kursbezogener Infotext für Hinweise an Studierende.
- Datenschutz-Export für in Terminen und Wartelisten gespeicherte Nutzerdaten.
- Aufräumen von Daten bei gelöschten Nutzern, gelöschten Kursen, Kursaustritten und Nutzer-Migrationen.

### Rollen und Nutzung

Studierende bzw. Kursmitglieder mit mindestens `autor`-Rechten sehen die freigegebenen Lostermine eines Kurses. Sie können sich auf die Losliste eines Termins eintragen oder ihren Eintrag wieder entfernen. Nach einer erfolgreichen Auslosung sehen sie ihre zugewiesenen Termine unter "Meine Termine".

Kursrollen mit mindestens `tutor`-Rechten erhalten zusätzlich den Administrationsbereich. Dort können sie Lospools anlegen, Termine erfassen, Termine freigeben oder zurück in den Entwurfsmodus verschieben, zugewiesene Personen entfernen und die Auslastung sowie die Loshistorie einsehen. Außerdem können sie den Infotext und die globalen Mailvorlagen bearbeiten.

Root-Nutzer können das Plugin aktivieren. Für normale Aktivierung ist das Plugin auf Kurskontexte beschränkt und nicht für Studiengruppen oder Institute vorgesehen. Im Code ist die Aktivierung außerdem auf Kurse des Instituts "Klinische Psychologie und Psychotherapie" eingeschränkt.

### Ablauf

1. Eine berechtigte Person aktiviert das Plugin im Kurs.
2. Im Kurs wird mindestens ein Lospool mit Name, Loszeitpunkt und Mailvorlage angelegt.
3. Termine werden angelegt, einem Pool zugeordnet und freigegeben.
4. Studierende tragen sich auf eine oder mehrere Loslisten ein.
5. Sobald der Loszeitpunkt eines Pools erreicht ist, verarbeitet der Stud.IP-Cronjob die fälligen Pools.
6. Das Plugin speichert die aktuelle Losliste als Historie, lost pro Termin eine verfügbare Person aus, entfernt gewinnende Personen aus weiteren Loslisten desselben Pools und versendet Benachrichtigungen.
7. Der Pool wird als gelost markiert.

### Mailvorlagen

Die globalen Mailvorlagen unterstützen folgende Platzhalter:

- `##fullname##`: vollständiger Name der zugelosten Person
- `##therapist##`: Beschreibung des Termins, üblicherweise Name der betreuenden Person
- `##date##`: Datum des Termins
- `##time##`: Uhrzeit des Termins
- `##weekday##`: Wochentag des Termins
- `##fs_start##`: Startdatum des Fallseminars
- `##fs_slot##`: Zeitfenster des Fallseminars
- `##fs_room##`: Raum des Fallseminars
- `##ko_date##`: Datum und Uhrzeit des Kick-Off-Termins
- `##ko_room##`: Raum des Kick-Off-Termins

### Installation und Build

Das Plugin benötigt Stud.IP ab Version 5.3 und ist laut Manifest bis Stud.IP 6.0.99 vorgesehen.

Nach dem Auschecken im Stud.IP-Pluginverzeichnis:

```bash
composer install
npm install
npm run build
```

Für Entwicklung am Vue-Frontend:

```bash
npm run dev
```

Ein Release-ZIP kann über das npm-Script erzeugt werden:

```bash
npm run zip
```

### Tests

Die Tests werden mit Codeception ausgeführt:

```bash
npm run tests
```

## English

LuckyConsultation is a Stud.IP plugin for lottery-based assignment of consultation or appointment slots inside courses. Teachers or other privileged course roles create appointment pools, students join waiting lists, and a Stud.IP cron job assigns available slots by drawing lots at configured times.

The plugin is intended for cases where multiple comparable appointments need to be distributed fairly and a simple first-come-first-served list is not appropriate.

### Features

- Management of lottery pools with individual draw dates.
- Creation, editing, and approval of appointment slots inside a pool.
- Separation between draft appointments and approved appointments.
- Student self-registration and removal from waiting lists.
- Display of already assigned appointments for students.
- Display of occupancy, waiting lists, and draw history for privileged course roles.
- Automatic drawing through a Stud.IP cron job.
- Avoidance of near-time duplicate assignments by checking for already assigned appointments within one hour before or after the current slot.
- Stud.IP message notification for the assigned student and the assigned therapist or supervisor.
- Global mail templates for the `PP` and `KJP` template types with placeholders.
- Course-specific information text for student-facing instructions.
- Privacy export for user data stored in appointments and waiting lists.
- Cleanup when users are deleted, courses are deleted, users leave a course, or user accounts are migrated.

### Roles and Usage

Students or course members with at least `autor` permission can see approved lottery appointments in a course. They can join or leave the waiting list for an appointment. After a successful draw, assigned appointments are shown under "Meine Termine".

Course roles with at least `tutor` permission also get access to the administration area. They can create lottery pools, create appointments, approve appointments or move them back to draft state, remove assigned users, and inspect occupancy and draw history. They can also edit the course information text and the global mail templates.

Root users can activate the plugin. For regular activation, the plugin is limited to course contexts and is not intended for study groups or institutes. The code additionally restricts activation to courses belonging to the institute "Klinische Psychologie und Psychotherapie".

### Workflow

1. A privileged user activates the plugin in a course.
2. At least one lottery pool is created with a name, draw date, and mail template.
3. Appointment slots are created, assigned to a pool, and approved.
4. Students join one or more waiting lists.
5. When a pool's draw date is reached, the Stud.IP cron job processes due pools.
6. The plugin stores the current waiting list as history, randomly assigns an available user per appointment, removes winners from other waiting lists in the same pool, and sends notifications.
7. The pool is marked as drawn.

### Mail Templates

The global mail templates support these placeholders:

- `##fullname##`: full name of the assigned person
- `##therapist##`: appointment description, usually the name of the therapist or supervisor
- `##date##`: appointment date
- `##time##`: appointment time
- `##weekday##`: appointment weekday
- `##fs_start##`: case seminar start date
- `##fs_slot##`: case seminar time slot
- `##fs_room##`: case seminar room
- `##ko_date##`: kick-off date and time
- `##ko_room##`: kick-off room

### Installation and Build

The plugin requires Stud.IP 5.3 or newer and is declared compatible up to Stud.IP 6.0.99 in the manifest.

After checking it out into the Stud.IP plugin directory:

```bash
composer install
npm install
npm run build
```

For Vue frontend development:

```bash
npm run dev
```

A release ZIP can be created through the npm script:

```bash
npm run zip
```

### Tests

Tests are run with Codeception:

```bash
npm run tests
```
