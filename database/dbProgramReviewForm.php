<?php
//todo: get info to the database
//get info to the database

class Review {
    private $id;
    private $family_id;
    private $feedback;
    private $program;

    public function __construct($id, $family_id, $program, $feedback){
        $this->id = $id;
        $this->family_id = $family_id;
        $this->program = $program;
        $this->feedback = $feedback;
    }

    public function getID(){
        return $this->id;
    }
    public function getFamily(){
        return $this->family_id;
    }
    public function getProgram(){
        return $this->program;
    }
    public function getFeedback(){
        return $this->feedback;
    }
}

function make_a_review($result_row){
    $review = new Review(
        $result_row['id'],
        $result_row['family_id'],
        $result_row['event_name'],
        $result_row['reviewText']
    );
    return $review;
}

function find_reviews($last_name, $email){
    include_once(dirname(__FILE__).'/../domain/Family.php');
    // Build query
    $where = 'WHERE ';
    $joins = '';
    $first = true;
    if($last_name == null && $email == null){
        $query = "SELECT * FROM dbProgramReviewForm ORDER BY family_id";
    }
    else if($email == null){
        $target = retrieve_family_by_lastName($last_name);
        $i = $target[0]->getID();
        $query = "SELECT * FROM dbProgramReviewForm WHERE family_id=$i";
    }
    else{
        $target = retrieve_family_by_email($email);
        $i = $target->getID();
        $query = "SELECT * FROM dbProgramReviewForm WHERE family_id=$i";
    }
    $connection = connect();
    $result = mysqli_query($connection, $query);
    if(!$result){
        mysqli_close($connection);
        return [];
    }

    $raw = mysqli_fetch_all($result, MYSQLI_ASSOC);
    $reviews = [];
    foreach ($raw as $row){
        $reviews [] = make_a_review($row);
    }
    mysqli_close($connection);
    return $reviews;

    //make queries to retrieve shit via last name

}

?>