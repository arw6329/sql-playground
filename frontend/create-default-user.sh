#!/bin/sh
npx wrangler d1 execute sqlworksheet-auth --local --command='
    INSERT INTO identities VALUES ("09752ad4-aefd-4e2d-9409-dbdf6e396ba5");
    INSERT INTO unique_identifiers VALUES (
        "10ba9336-5212-4d09-94ce-bf65212db5c7",
        "09752ad4-aefd-4e2d-9409-dbdf6e396ba5",
        "admin@sqlworksheet",
        "email"
    );
    INSERT INTO passwords VALUES (
        "0075dd74-efbd-431d-937f-7b4829d00847",
        "09752ad4-aefd-4e2d-9409-dbdf6e396ba5",
        "$argon2id$v=19$m=65536,t=3,p=4$LJRXwOUD/CdLYPqllM24PA$F6prCQhhNjez/TtMreq0Xtn4IJ8dHiKzsVItU0EmOE4"
    ); 
'
