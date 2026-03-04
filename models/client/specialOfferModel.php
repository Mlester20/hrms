<?php

    class SpecialOfferModel{
        private $con;

        public function __construct($con)
        {
            $this->con = $con;
        }

        protected $special_offers = 'special_offers';

        public function getMenus(){
            $offers = [];

            $offers_query = "SELECT * FROM {$this->special_offers} ORDER BY offers_id";
            $offers_result = mysqli_query($this->con, $offers_query);

            if ($offers_result) {
                $offers = mysqli_fetch_all($offers_result, MYSQLI_ASSOC);
            }

            return $offers;
        }
    }

?>