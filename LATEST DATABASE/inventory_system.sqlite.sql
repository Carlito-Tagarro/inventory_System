-- SQLite3 version of inventory_system with foreign keys

PRAGMA foreign_keys = ON;

-- Table: brochures
CREATE TABLE brochures (
  brochure_id INTEGER PRIMARY KEY AUTOINCREMENT,
  brochure_name TEXT NOT NULL,
  quantity INTEGER NOT NULL,
  total_brochure INTEGER NOT NULL
);

INSERT INTO brochures (brochure_id, brochure_name, quantity, total_brochure) VALUES
(1, 'ALL PROGRAMS', 15, 0),
(2, 'ADOBE', 9, 0),
(3, 'AUTODESK', 91, 0),
(4, 'AWS', 80, 0),
(5, 'BENEFITS OF MICROSOFT', 98, 0),
(6, 'CCS', 0, 0),
(7, 'CISCO', 85, 0),
(8, 'COPILOT', 35, 0),
(9, 'CSB', 134, 0),
(10, 'ESB', 80, 0),
(11, 'IC3', 171, 0),
(12, 'ITS', 9, 0),
(13, 'META', 113, 0),
(14, 'MICROSOFT CERTIFICATIONS', 59, 0),
(15, 'PMI', 377, 0),
(16, 'QUICKBOOKS', 9, 0),
(17, 'SWIFT', 138, 0),
(18, 'THE VALUE', 0, 0),
(19, 'UNITY', 100, 0),
(20, 'VALUE OF CERTIFICATIONS', 38, 0),
(21, 'VERSANT', 77, 0),
(22, 'ALL PROGRAMS (TRIFOLD)', 0, 0),
(23, 'Agriscience and Technology Careers', 25, 0),
(24, 'Health Sciences Careers', 0, 0),
(25, 'Hospitality and Culinary Arts', 0, 0);

-- Table: event_form
CREATE TABLE event_form (
  event_form_id INTEGER PRIMARY KEY AUTOINCREMENT,
  event_name TEXT NOT NULL,
  event_title TEXT NOT NULL,
  event_date TEXT NOT NULL,
  date_time_ingress TEXT NOT NULL,
  date_time_egress TEXT NOT NULL,
  place TEXT NOT NULL,
  location TEXT NOT NULL,
  sponsorship_budg TEXT NOT NULL,
  target_audience TEXT,
  number_audience INTEGER NOT NULL,
  set_up TEXT,
  booth_size TEXT,
  booth_inclusion TEXT,
  number_tables INTEGER,
  number_chairs INTEGER,
  speaking_slot TEXT,
  date_time TEXT NOT NULL,
  program_target TEXT,
  technical_team TEXT NOT NULL,
  trainer_needed TEXT NOT NULL,
  ready_to_use TEXT,
  provide_materials TEXT NOT NULL,
  created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  request_status TEXT NOT NULL,
  user_id INTEGER,
  request_mats INTEGER,
  FOREIGN KEY (user_id) REFERENCES users(user_id),
  FOREIGN KEY (request_mats) REFERENCES material_request_form(material_request_id) ON DELETE SET NULL ON UPDATE CASCADE
);

-- Table: event_form_history
CREATE TABLE event_form_history (
  event_form_id INTEGER PRIMARY KEY,
  event_name TEXT,
  event_title TEXT,
  event_date TEXT,
  sender_email TEXT,
  date_time_ingress TEXT,
  date_time_egress TEXT,
  place TEXT,
  location TEXT,
  sponsorship_budg TEXT NOT NULL,
  target_audience TEXT,
  number_audience INTEGER,
  set_up TEXT,
  booth_size TEXT,
  booth_inclusion TEXT,
  number_tables INTEGER,
  number_chairs INTEGER,
  speaking_slot TEXT,
  date_time TEXT,
  program_target TEXT,
  technical_team TEXT,
  trainer_needed TEXT,
  ready_to_use TEXT,
  provide_materials TEXT,
  created_at TEXT,
  request_status TEXT,
  processed_at TEXT,
  user_id INTEGER,
  request_mats INTEGER,
  FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Table: marketing_materials
CREATE TABLE marketing_materials (
  material_id INTEGER PRIMARY KEY AUTOINCREMENT,
  material_name TEXT NOT NULL,
  quantity INTEGER NOT NULL,
  others TEXT
);

INSERT INTO marketing_materials (material_id, material_name, quantity, others) VALUES
(6, 'MATERIALS 1', 1, NULL),
(7, 'MATERIALS 2', 1, NULL),
(8, 'MATERIALS 3', 1, NULL),
(9, 'MATERIALS 4', 1, NULL),
(10, 'MATERIALS 5', 1, NULL);

-- Table: material_request_form
CREATE TABLE material_request_form (
  material_request_id INTEGER PRIMARY KEY AUTOINCREMENT,
  request_mats INTEGER NOT NULL,
  name_brochures TEXT NOT NULL,
  brochure_quantity INTEGER NOT NULL,
  name_swag TEXT NOT NULL,
  swag_quantity INTEGER NOT NULL,
  name_material TEXT NOT NULL,
  material_quantity INTEGER NOT NULL
);

-- Table: material_return_request
CREATE TABLE material_return_request (
  request_id INTEGER PRIMARY KEY AUTOINCREMENT,
  event_id INTEGER NOT NULL,
  user_id INTEGER NOT NULL,
  items_json TEXT NOT NULL,
  status TEXT NOT NULL DEFAULT 'Pending',
  requested_at TEXT NOT NULL,
  reviewed_at TEXT,
  FOREIGN KEY (event_id) REFERENCES event_form(event_form_id),
  FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Table: swags
CREATE TABLE swags (
  swag_id INTEGER PRIMARY KEY AUTOINCREMENT,
  swags_name TEXT NOT NULL,
  quantity INTEGER NOT NULL
);

INSERT INTO swags (swag_id, swags_name, quantity) VALUES
(1, 'SWAGS 1', 1),
(2, 'SWAGS 2', 1),
(3, 'SWAGS 3', 1),
(4, 'SWAGS 4', 1),
(5, 'SWAGS 5', 1);

-- Table: users
CREATE TABLE users (
  user_id INTEGER PRIMARY KEY AUTOINCREMENT,
  username TEXT NOT NULL,
  password TEXT NOT NULL,
  email TEXT NOT NULL,
  user_type TEXT,
  Account_status TEXT NOT NULL,
  verification_code TEXT NOT NULL,
  verified INTEGER DEFAULT 0,
  reset_token TEXT NOT NULL,
  token_expiry TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (user_id, username, password, email, user_type, Account_status, verification_code, verified, reset_token, token_expiry) VALUES
(13, 'administration', '$2y$10$oJS7luv6feZ6hvkqZR0.ReJYZ/0LcUk1v83n/Sm0EakRN96Cvt7lW', 'carlitotagarro0@gmail.com', 'admin', '', '921859', 1, '', '0000-00-00 00:00:00'),
(16, 'Joseph', '$2y$10$cmBgp4rPBQ9OeNkUsVlSd.Fk0BsnC1OSX.pvpaONNsmJGx0DOm/vC', 'ch4rlestzy27@gmail.com', 'trainer', 'Activated', '470291', 1, '', '2025-08-27 11:19:40'),
(17, 'Charles', '$2y$10$fHwDU6Aa/NyPGFp7IKes5uvGXTWaVKAMuNY4dbLdWKX3ZZSxDFeVS', 'carlitotagarro27@gmail.com', 'trainer', 'Activated', '412464', 1, '', '0000-00-00 00:00:00'),
(22, 'Charlito', '$2y$10$zT3YGflFo1b4bBgS/uIOFOOvFxJOzR8L2gu67A3hSwx.sY7YsySiC', 'carlitotagarro0927@gmail.com', 'trainer', 'Deactivated', '700235', 1, '', '2025-08-27 11:19:40');
INSERT INTO users (user_id, username, password, email, user_type, Account_status, verification_code, verified, reset_token, token_expiry) VALUES
(13, 'administration', '$2y$10$oJS7luv6feZ6hvkqZR0.ReJYZ/0LcUk1v83n/Sm0EakRN96Cvt7lW', 'carlitotagarro0@gmail.com', 'admin', '', '921859', 1, '', '0000-00-00 00:00:00'),
(16, 'Joseph', '$2y$10$cmBgp4rPBQ9OeNkUsVlSd.Fk0BsnC1OSX.pvpaONNsmJGx0DOm/vC', 'ch4rlestzy27@gmail.com', 'trainer', 'Activated', '470291', 1, '', '2025-08-27 11:19:40'),
(17, 'Charles', '$2y$10$fHwDU6Aa/NyPGFp7IKes5uvGXTWaVKAMuNY4dbLdWKX3ZZSxDFeVS', 'carlitotagarro27@gmail.com', 'trainer', 'Activated', '412464', 1, '', '0000-00-00 00:00:00'),
(22, 'Charlito', '$2y$10$zT3YGflFo1b4bBgS/uIOFOOvFxJOzR8L2gu67A3hSwx.sY7YsySiC', 'carlitotagarro0927@gmail.com', 'trainer', 'Deactivated', '700235', 1, '', '2025-08-27 11:19:40');
