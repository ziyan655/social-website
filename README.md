# social-website
  A simple music social website that users can register and interact with each others to get information on ongoing concerts. 

Instruction:

- LAMP is used to run this website

- Import website.sql file into your database. The fields that needs changing are marked with TODOs

- Go to 'include.php' file and make necessary changes. Also marked as TODOs

- Examine the E-R diagram and relational schema to have a whole grasp of the site

- Run 'index.php' on apache server

Enjoy!




Features:

- Search concerts with filters
- Follow other users, artists, and bands
- User/Artist registration
- Posting reviews, status wall
- Bookmark search results, others profile page
- News feed upon login including recommended concerts



Assumptions:

- Users are not artists. No overlapping for the user and artist sets.
- Users can like a artist or a band. Users can follow another user. Users can't follow artist/band, can't like users. An artist/band can't follow users or like other bands.
- An artist does not have to belong to a band. A concert can be held by one single artist or one band.
- Upon registration, if a user indicated his favorite artist/band, then that means he likes the artist/band. So he automatically become a fan of that artist/band. This can also be done as liking an artist/band on their homepage.
- locationId and cityId is globally unique.
- An artist only have one music main category and one subcategory.
- A band only have one music main category and one subcategory.
- When a user likes a band or artist, it does not indicates that they have the musical taste associated with those bands or artists. The user needs to specify taste independently.
- If a user becomes a fan of a particular band, it does not logically infer that the user is a fan of any artist in the band.
- User-Artist and User-Band tables are independent from each other, inserting into one table does not update the other.
- Assume tables “City” “Country” “Location” contain all the available cities, countries, locations in the world.
