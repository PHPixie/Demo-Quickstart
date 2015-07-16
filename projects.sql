CREATE TABLE `projects`(
    `id`         INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name`       VARCHAR(255),
    `tasksTotal` INT DEFAULT 0,
    `tasksDone`  INT DEFAULT 0
);

CREATE TABLE `tasks`(
    `id`        INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `projectId` INT NOT NULL,
    `name`      VARCHAR(255),
    `isDone`    BOOLEAN DEFAULT 0
);

INSERT INTO `projects` VALUES
(1, 'Quickstart', 4, 3),
(2, 'Build a website', 3, 0);

INSERT INTO `tasks` VALUES
(1, 1, 'Installing', 1),
(2, 1, 'Routing', 1),
(3, 1, 'Templating', 1),
(4, 1, 'Database', 0),

(5, 2, 'Design', 0),
(6, 2, 'Develop', 0),
(7, 2, 'Deploy', 0);