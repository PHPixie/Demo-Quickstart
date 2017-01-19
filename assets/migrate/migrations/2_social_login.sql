ALTER TABLE users ADD COLUMN twitterId VARCHAR(255) AFTER passwordHash;

-- statement

ALTER TABLE users ADD COLUMN facebookId VARCHAR(255) AFTER twitterId;