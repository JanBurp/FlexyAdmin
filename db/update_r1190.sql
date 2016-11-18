# Update to CodeIgniter 2.0.3

CREATE INDEX last_activity_idx ON cfg_sessions(last_activity);
ALTER TABLE cfg_sessions MODIFY user_agent VARCHAR(120);