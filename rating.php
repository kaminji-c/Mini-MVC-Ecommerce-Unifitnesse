<?php/*  Star Rating  */
                $pid = $product['product_id'];
                echo '<span id="response' . $pid . '">';

                $avg = $dbh->prepare("select avg(rating) as average from rating where productid = '$pid'");

                $avg->execute();

                $avgrow = $avg->fetchObject();

                $score = $avgrow->average;

                for ($i = 1; $i <= 5; $i++) {
                    if ($i <= ceil($score)) {
                        echo '<i class="fas fa-star" height="15" width = "15" onclick="sendRequest(\'' . $pid . '\',\'' . $i . '\');"</i>';
                    } else {
                        echo  '<i class="far fa-star" height="15" width = "15" onclick="sendRequest(\'' . $pid . '\',\'' . $i . '\');"</i>';
                    }
                }
                echo '<span>';

                ?>