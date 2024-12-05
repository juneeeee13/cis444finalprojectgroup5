1. Submit posts/replies
-- Insert a new post into the 'posts' table.
-- The 'poster_id' references the user who is creating the post.
-- 'title' is the title of the post, 'content' is the main text content of the post.
-- 'images' stores the image file path or URL associated with the post.
-- 'created_at' automatically stores the current timestamp (using NOW()).
-- 'hashtags' stores hashtags associated with the post for easy searching.
INSERT INTO posts (poster_id, title, content, images, created_at, hashtags) 
VALUES (1, 'Sample Title', 'Sample Content', 'sample_image.jpg', NOW(), '#sample');

-- Insert a reply into the 'replies' table for a specific post.
-- 'post_id' references the post being replied to.
-- 'user_id' references the user creating the reply.
-- 'content' is the reply text, and 'image' stores an optional image for the reply.
INSERT INTO replies (post_id, user_id, content, image) 
VALUES (1, 1, 'Sample Reply Content', 'reply_image.jpg');


2. Display posts/replies
-- Retrieve all posts from the 'posts' table.
-- The posts are ordered by 'created_at' in descending order, so the most recent post appears first.
SELECT * FROM posts ORDER BY created_at DESC;

-- Retrieve all replies for a specific post from the 'replies' table.
-- Only replies linked to 'post_id = 1' are selected.
-- Replies are ordered by 'reply_id' in ascending order to display them in chronological order.
SELECT * FROM replies WHERE post_id = 1 ORDER BY reply_id ASC;


3. Submit reports
-- Insert a new report into the 'reports' table.
-- 'post_id' references the post being reported.
-- 'reporter_id' references the user creating the report.
-- 'user_reported' references the user who is being reported for the post.
INSERT INTO reports (post_id, reporter_id, user_reported) 
VALUES (1, 1, 2);



4. Sort posts
-- Retrieve all posts from the 'posts' table.
-- Sort the posts by 'created_at' in descending order to display the most recent posts first.
SELECT * FROM posts ORDER BY created_at DESC;

-- Retrieve all posts from the 'posts' table.
-- Sort the posts by 'like_no' in descending order to display the most liked posts first.
SELECT * FROM posts ORDER BY like_no DESC;


5. Display sorted posts
-- Retrieve and display all posts sorted by 'created_at' in descending order.
-- This query shows the most recent posts first.
SELECT * FROM posts ORDER BY created_at DESC;

-- Alternatively, retrieve and display all posts sorted by 'like_no' in descending order.
-- This query shows the posts with the highest number of likes first.
SELECT * FROM posts ORDER BY like_no DESC;



6. Search posts based on hashtags
-- Search for posts that contain a specific hashtag in the 'hashtags' column.
-- The '%#sample%' pattern searches for any occurrence of '#sample' within the 'hashtags' column.
SELECT * FROM posts WHERE hashtags LIKE '%#sample%';




7. Display searched posts
-- Retrieve posts from the 'posts' table where the 'title', 'content', or 'hashtags' match the search keyword.
-- The keyword is searched as a partial match using the '%' wildcard.
-- This query allows for flexible searching based on different columns.
SELECT * FROM posts 
WHERE title LIKE '%sample%' 
   OR content LIKE '%sample%' 
   OR hashtags LIKE '%#sample%';



8. Ranking calculation based on like counts
-- Retrieve the 'post_id' and 'like_no' (number of likes) from the 'posts' table.
-- Sort the results by 'like_no' in descending order to calculate the ranking of posts based on likes.
SELECT post_id, like_no 
FROM posts 
ORDER BY like_no DESC;



9. Display ranking posts based on like counts
-- Retrieve all columns from the 'posts' table.
-- Sort the posts by 'like_no' in descending order to display the posts with the most likes first.
SELECT * FROM posts 
ORDER BY like_no DESC;



10. Delete posts/replies

-- Delete all reports from the 'reports' table associated with a specific post ID.
-- This ensures no foreign key conflicts when deleting the post itself.
DELETE FROM reports WHERE post_id = 1;

-- Delete a specific reply from the 'replies' table using its unique 'reply_id'.
-- This is used to remove an individual reply without affecting other replies.
DELETE FROM replies WHERE reply_id = 1;

-- Delete a specific post from the 'posts' table using its unique 'post_id'.
-- Ensure all dependent rows in other tables (like 'replies' and 'reports') are removed first.
DELETE FROM posts WHERE post_id = 1;


