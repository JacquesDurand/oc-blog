CREATE TABLE IF NOT EXISTS users (
    id serial PRIMARY KEY NOT NULL ,
    username VARCHAR (100) UNIQUE NOT NULL ,
    password VARCHAR ( 50 ) NOT NULL,
    email VARCHAR ( 255 ) UNIQUE NOT NULL,
    role INT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
    last_activity TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS category(
  id serial PRIMARY KEY NOT NULL,
  name VARCHAR (50) UNIQUE NOT NULL
);

CREATE TABLE IF NOT EXISTS post (
    id serial PRIMARY KEY NOT NULL ,
    title VARCHAR (255) UNIQUE NOT NULL ,
    lede VARCHAR ( 255 ) NOT NULL,
    content TEXT NOT NULL,
    slug VARCHAR (50) NOT NULL,
    state INT NOT NULL,
    category_id INT,
    author_id INT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
    CONSTRAINT fk_category FOREIGN KEY (category_id) REFERENCES category(id) ON DELETE SET NULL,
    CONSTRAINT fk_author FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS comment (
    content TEXT NOT NULL,
    state INT NOT NULL,
    moderation_reason VARCHAR (255),
    author_id INT NOT NULL,
    post_id INT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
    CONSTRAINT fk_author FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_post FOREIGN KEY (post_id) REFERENCES post(id) ON DELETE CASCADE
);

CREATE FUNCTION update_updated_at_column() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    NEW.updated_at = NOW();
RETURN NEW;
END;
$$;

CREATE TRIGGER user_updated_at_modification_time BEFORE UPDATE ON users FOR EACH ROW EXECUTE PROCEDURE update_updated_at_column();
CREATE TRIGGER post_updated_at_modification_time BEFORE UPDATE ON post FOR EACH ROW EXECUTE PROCEDURE update_updated_at_column();
CREATE TRIGGER comment_updated_at_modification_time BEFORE UPDATE ON comment FOR EACH ROW EXECUTE PROCEDURE update_updated_at_column();

INSERT INTO category(name) VALUES ('category1');
INSERT INTO category(name) VALUES ('category2');
INSERT INTO category(name) VALUES ('category3');
