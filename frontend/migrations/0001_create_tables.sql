-- Migration number: 0001 	 2025-04-11T22:47:42.093Z
CREATE TABLE identities (
    _id CHARACTER(36) NOT NULL PRIMARY KEY
);

CREATE TABLE unique_identifiers (
    _id CHARACTER(36) NOT NULL PRIMARY KEY,
    identity_id CHARACTER(36) NOT NULL,
    identifier VARCHAR(255) NOT NULL UNIQUE,
    identifier_type VARCHAR(20) NOT NULL,

    FOREIGN KEY (identity_id) REFERENCES identities(_id)
);

CREATE TABLE passwords (
    _id CHARACTER(36) NOT NULL PRIMARY KEY,
    identity_id CHARACTER(36) NOT NULL UNIQUE,
    hash CHARACTER(97) NOT NULL,

    FOREIGN KEY (identity_id) REFERENCES identities(_id)
);

CREATE TABLE sessions (
    _id CHARACTER(36) NOT NULL PRIMARY KEY,
    identity_id CHARACTER(36) NOT NULL,
    session_secret CHARACTER(50) NOT NULL UNIQUE,

    FOREIGN KEY (identity_id) REFERENCES identities(_id)
);
