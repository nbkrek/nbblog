<?php

        $this->c['db'] = function ($c) {
            $db = new \PDO('sqlite:/tmp/nbblogtest.db');
            $db->exec('PRAGMA foreign_keys = ON;');
            $db->exec("CREATE TABLE IF NOT EXISTS articles (
                           id INTEGER PRIMARY KEY, 
                           stub TEXT, 
                           publishdate DATETIME)");

            $db->exec("CREATE TABLE IF NOT EXISTS tags (
                           id INTEGER PRIMARY KEY, 
                           tag TEXT
                           )");

            $db->exec("CREATE UNIQUE INDEX IF NOT EXISTS tagidx ON tags(tag)");

            $db->exec("CREATE TABLE IF NOT EXISTS languages (
                           id INTEGER PRIMARY KEY, 
                           language TEXT
                           )");

            $db->exec("CREATE UNIQUE INDEX IF NOT EXISTS languageidx ON languages(language)");

            $db->exec("CREATE TABLE IF NOT EXISTS tags2articles (
                           tagid INTEGER,
                           articleid INTEGER,
                           FOREIGN KEY(tagid) REFERENCES tags(id),
                           FOREIGN KEY(articleid) REFERENCES articles(id)
                           )");

            $db->exec("CREATE TABLE IF NOT EXISTS articles2languages (
                           languageid INTEGER,
                           articleid INTEGER,
                           FOREIGN KEY(languageid) REFERENCES languages(id),
                           FOREIGN KEY(articleid) REFERENCES articles(id)
                           )");

            return $db;
        };
