/* список типов контента для поста */
INSERT INTO content_types (type_name, type_class)
VALUES
('Цитата', 'quote'),
('Ссылка', 'link'),
('Фото', 'photo'),
('Видео', 'video'),
('Текст', 'text');

/* придумайте пару пользователей*/
INSERT INTO users (username, email, avatar, password)
VALUES
('Эльвира', 'elvira@mail.ru', 'userpic-elvira.jpg', '012345'),
('Петро', 'petro@rambler.ru', 'userpic-petro.jpg', '123456'),
('Лариса', 'larisa@gmail.ru', 'userpic-larisa-small.jpg', '234567'),
('Владик', 'vladik@ya.ru', 'userpic.jpg', '345678'),
('Виктор', 'viktor@bk.ru', 'userpic-mark.jpg', '456789');

/*существующий список постов */
INSERT INTO posts (heading, post_type, content, author_id, view_count)
VALUES
('Цитата', 1,  'Мы в жизни любим только раз, а после ищем лишь похожих', 3, 10),
('Игра престолов', 5, 'Не могу дождаться начала финального сезона своего любимого сериала!', 4, 3),
('Наконец, обработал фотки!', 3, 'rock-medium.jpg', 3, 49),
('Моя мечта', 3, 'coast-medium.jpg', 3, 25),
('Лучшие курсы', 2, 'www.htmlacademy.ru', 5, 13);

/* придумайте пару комментариев к разным постам */
INSERT INTO comments SET user_id = 2, post_id = 4, content = 'тестовый комментарий 1';
INSERT INTO comments SET user_id = 1, post_id = 5, content = 'тестовый комментарий 2';

/* получить список постов с сортировкой по популярности и вместе с именами авторов и типом контента */
SELECT posts.content, posts.view_count, users.username, content_types.type_name FROM posts
INNER JOIN users ON posts.author_id=users.id
INNER JOIN content_types ON posts.post_type=content_types.id
ORDER  BY view_count;

/* получить список постов для конкретного пользователя; */
SELECT * FROM posts WHERE author_id=5;

/*получить список комментариев для одного поста, в комментариях должен быть логин пользователя */
SELECT comments.content, users.username FROM comments
INNER JOIN users ON comments.user_id=users.id
WHERE comments.post_id=5;

/* добавить лайк к посту */
INSERT INTO likes SET user_id=3, post_id=3;

/* подписаться на пользователя */
INSERT INTO subscribe SET follower_id=4, author_id=5;