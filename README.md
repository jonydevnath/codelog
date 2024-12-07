INSERT INTO users (
    username, user_email, user_pass, name, phone, country, web_url, birthday, gender, language
) VALUES (
    'johndoe', 'johndoe@example.com', 'hashedpassword123', 'John Doe', '1234567890', 'USA', 'https://example.com', '1990-01-01', 'Male', 'English'
);


CREATE TABLE users (
    user_id INT(11) NOT NULL AUTO_INCREMENT,
    username VARCHAR(255) NOT NULL,
    user_email VARCHAR(255) NOT NULL,
    user_pass VARCHAR(255) NOT NULL,
    name VARCHAR(255) NULL,
    phone VARCHAR(14) NULL,
    country VARCHAR(255) NULL,
    web_url VARCHAR(255) NULL,
    birthday DATE NULL,
    gender VARCHAR(255) NULL,
    language VARCHAR(255) NULL,
    PRIMARY KEY (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE users (
    user_id INT(11) NOT NULL AUTO_INCREMENT,
    username VARCHAR(255) NOT NULL,
    user_email VARCHAR(255) NOT NULL,
    user_pass VARCHAR(255) NOT NULL,
    PRIMARY KEY (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
