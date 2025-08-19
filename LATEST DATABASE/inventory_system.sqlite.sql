-- SQLite3 version of inventory_system

-- Table: brochures
CREATE TABLE brochures (
  brochure_id INTEGER PRIMARY KEY AUTOINCREMENT,
  brochure_name TEXT NOT NULL,
  quantity INTEGER NOT NULL,
  total_brochure INTEGER NOT NULL
);

INSERT INTO brochures (brochure_id, brochure_name, quantity, total_brochure) VALUES
(1, 'BROCHURE 1', 5, 0),
(2, 'BROCHURE 2', 5, 0),
(3, 'BROCHURE 3', 20, 0),
(4, 'BROCHURE 4', 20, 0),
(5, 'BROCHURE 5', 20, 0);

-- Table: event_form
CREATE TABLE event_form (
  event_form_id INTEGER PRIMARY KEY AUTOINCREMENT,
  event_name TEXT NOT NULL,
  event_title TEXT NOT NULL,
  event_date DATE NOT NULL,
  date_time_ingress DATETIME NOT NULL,
  date_time_egress DATETIME NOT NULL,
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
  date_time DATETIME NOT NULL,
  program_target TEXT,
  technical_team TEXT NOT NULL,
  trainer_needed TEXT NOT NULL,
  ready_to_use TEXT,
  provide_materials TEXT NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  request_status TEXT NOT NULL,
  user_id INTEGER,
  request_mats INTEGER,
  FOREIGN KEY (user_id) REFERENCES users(user_id),
  FOREIGN KEY (request_mats) REFERENCES material_request_form(material_request_id)
);

-- Table: event_form_history
CREATE TABLE event_form_history (
  event_form_id INTEGER PRIMARY KEY,
  event_name TEXT,
  event_title TEXT,
  event_date DATE,
  sender_email TEXT,
  date_time_ingress DATETIME,
  date_time_egress DATETIME,
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
  date_time DATETIME,
  program_target TEXT,
  technical_team TEXT,
  trainer_needed TEXT,
  ready_to_use TEXT,
  provide_materials TEXT,
  created_at DATETIME,
  request_status TEXT,
  processed_at DATETIME,
  user_id INTEGER,
  request_mats INTEGER,
  FOREIGN KEY (user_id) REFERENCES users(user_id),
  FOREIGN KEY (request_mats) REFERENCES material_request_form(material_request_id)
);

INSERT INTO event_form_history (event_form_id, event_name, event_title, event_date, sender_email, date_time_ingress, date_time_egress, place, location, sponsorship_budg, target_audience, number_audience, set_up, booth_size, booth_inclusion, number_tables, number_chairs, speaking_slot, date_time, program_target, technical_team, trainer_needed, ready_to_use, provide_materials, created_at, request_status, processed_at, user_id, request_mats) VALUES
(44, 'TESTING OPERATION OF THE SYSTEM', 'OH NO', '2025-08-19', 'ch4rlestzy27@gmail.com', '2025-08-19 11:44:00', '2025-08-19 11:44:00', 'AGILE TECH', 'SOUTHWOODS', '', '', 0, '', '', '', 0, 0, '', '0000-00-00 00:00:00', '', 'No', 'No', '', 'Yes', '2025-08-19 11:44:45', 'Approved', '2025-08-19 11:48:03', 16, 293);

-- Table: marketing_materials
CREATE TABLE marketing_materials (
  material_id INTEGER PRIMARY KEY AUTOINCREMENT,
  material_name TEXT NOT NULL,
  quantity INTEGER NOT NULL,
  others TEXT
);

INSERT INTO marketing_materials (material_id, material_name, quantity, others) VALUES
(6, 'MATERIALS 1', 5, NULL),
(7, 'MATERIALS 2', 5, NULL),
(8, 'MATERIALS 3', 20, NULL),
(9, 'MATERIALS 4', 20, NULL),
(10, 'MATERIALS 5', 20, NULL);

-- Table: material_request_form
CREATE TABLE material_request_form (
  material_request_id INTEGER PRIMARY KEY AUTOINCREMENT,
  request_mats INTEGER NOT NULL,
  name_brochures TEXT NOT NULL,
  brochure_quantity INTEGER NOT NULL,
  name_swag TEXT NOT NULL,
  swag_quantity INTEGER NOT NULL,
  name_material TEXT NOT NULL,
  material_quantity INTEGER NOT NULL,
  FOREIGN KEY (request_mats) REFERENCES event_form(event_form_id)
);

INSERT INTO material_request_form (material_request_id, request_mats, name_brochures, brochure_quantity, name_swag, swag_quantity, name_material, material_quantity) VALUES
(293, 293, 'BROCHURE 1', 23, '', 0, '', 0),
(294, 293, 'BROCHURE 2', 21, '', 0, '', 0),
(295, 293, '', 0, '', 0, 'MATERIALS 1', 23),
(296, 293, '', 0, '', 0, 'MATERIALS 2', 21),
(297, 293, '', 0, 'SWAGS 1', 23, '', 0),
(298, 293, '', 0, 'SWAGS 2', 21, '', 0);

-- Table: material_return_request
CREATE TABLE material_return_request (
  request_id INTEGER PRIMARY KEY AUTOINCREMENT,
  event_id INTEGER NOT NULL,
  user_id INTEGER NOT NULL,
  items_json TEXT NOT NULL,
  status TEXT NOT NULL DEFAULT 'Pending',
  requested_at DATETIME NOT NULL,
  reviewed_at DATETIME,
  FOREIGN KEY (event_id) REFERENCES event_form(event_form_id),
  FOREIGN KEY (user_id) REFERENCES users(user_id)
);

INSERT INTO material_return_request (request_id, event_id, user_id, items_json, status, requested_at, reviewed_at) VALUES
(8, 44, 16, '[{\"type\":\"Brochure\",\"name\":\"BROCHURE 1\",\"qty\":5},{\"type\":\"Brochure\",\"name\":\"BROCHURE 2\",\"qty\":5},{\"type\":\"Marketing Material\",\"name\":\"MATERIALS 1\",\"qty\":5},{\"type\":\"Marketing Material\",\"name\":\"MATERIALS 2\",\"qty\":5},{\"type\":\"Swag\",\"name\":\"SWAGS 1\",\"qty\":5},{\"type\":\"Swag\",\"name\":\"SWAGS 2\",\"qty\":5}]', 'Approved', '2025-08-19 11:51:16', '2025-08-19 11:52:27');

-- Table: swags
CREATE TABLE swags (
  swag_id INTEGER PRIMARY KEY AUTOINCREMENT,
  swags_name TEXT NOT NULL,
  quantity INTEGER NOT NULL
);

INSERT INTO swags (swag_id, swags_name, quantity) VALUES
(1, 'SWAGS 1', 5),
(2, 'SWAGS 2', 5),
(3, 'SWAGS 3', 20),
(4, 'SWAGS 4', 20),
(5, 'SWAGS 5', 20);

-- Table: users
CREATE TABLE users (
  user_id INTEGER PRIMARY KEY AUTOINCREMENT,
  username TEXT NOT NULL,
  password TEXT NOT NULL,
  email TEXT NOT NULL,
  user_type TEXT,
  position TEXT NOT NULL,
  verification_code TEXT NOT NULL,
  verified INTEGER DEFAULT 0,
  full_name TEXT NOT NULL
);

INSERT INTO users (user_id, username, password, email, user_type, position, verification_code, verified, full_name) VALUES
(13, 'administration', '$2y$10$CP9GOla6e9kfDWiDpgDTsux.PJ0UpiXL2hBTudOpsGPAKeArFDr.W', 'carlitotagarro0@gmail.com', 'admin', '', '921859', 1, ''),
(14, 'user', '$2y$10$nZLjh4MA0DRpnCwQ7l4JTuXl6qwc6WQF28Zpt13eLpaamtPE7tPAW', 'carlitotagarro27@gmail.com', 'trainer', '', '290166', 1, ''),
(16, 'Joseph', '$2y$10$cmBgp4rPBQ9OeNkUsVlSd.Fk0BsnC1OSX.pvpaONNsmJGx0DOm/vC', 'ch4rlestzy27@gmail.com', 'trainer', '', '470291', 1, '');

-- Note: Foreign key constraints are omitted for simplicity. Add them if needed using SQLite's syntax.
