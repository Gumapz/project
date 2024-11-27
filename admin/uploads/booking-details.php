
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="icon" type="image/x-icon" href="../image/logo.jpg">
    <title>Chadoyven Car Rental</title>
    <style>
        .image-review{
            width: 150px;
            margin-top: 2%;
            margin-bottom: 1%;
        }

        .payment{
            width: 200px;
            margin-top: 2%;
            margin-bottom: 1%;
            box-shadow: 2px 2px 5px black;
        }

        .cancel{
            font-size: 15px;
            font-weight: 400;
            color: black;
            background: rgb(218, 217, 217);
            width: 60px;
            padding: 2px 10px;
            border-radius: 10px;
            cursor: pointer;
        }

        .cancel a{
            color: black;
        }


        .cancel:hover{
            font-weight: 600;
        }

        .button:hover{
            background-color: rgb(20, 100, 20);
        }

        form{
            margin: auto;
            margin-top:3%;
            margin-bottom:5%;
            width: 50%;
            height: auto;
            border: 1px;
            padding: 10px;
            z-index: 1;
            box-shadow: 2px 2px 5px black;
        }

        .logo{
            display: block;
            margin-left: auto;
            width: 100px;
        }
        .container {
            margin-left: 40%;
            display: grid;
            align-items: center; 
            grid-template-columns: 1fr 1fr 1fr;
            column-gap: 5px;
        }

        hr{
            border: 0;
            height: 3px;
            width: 100%;
            background: #ccc;
            margin: 15px 0 10px;
        }
        
        .date {
            display: flex;
            flex-wrap: wrap;
            gap: 20px; /* Adjust spacing between columns as needed */
        }
        .date h3 {
            flex: 1 1 200px; /* Adjust the base width as needed */
            margin: 0;
            padding: 5px;
            box-sizing: border-box;
        }

        .Details{
            width: 80%;
            margin: auto;
        }

        @media(max-width:991px){
            html{
                font-size: 50%;
            }
        }

        @media(max-width:768px){
            .form h4{
                font-size: 12px;
            }
            .button{
                font-size: 12px;
            }
            .cancel{
                font-size: 12px;
            }
            .payment{
                width: 100px;
            }
            .logo{
                width: 100px;
            }
        }

        @media(max-width:450px){
            html{
                font-size: 45%;
            }
        }
        
    </style>
</head>
<body>
    <!-- SIDEBAR -->
	<section id="sidebar">
		<a href="#" class="brand">
			<i class='bx bxs-smile'></i>
			<span class="text">Admin</span>
		</a>
		<ul class="side-menu top">
			<li class="active">
				<a href="#">
					<i class='bx bxs-dashboard' ></i>
					<span class="text">Dashboard</span>
				</a>
			</li>
			<!-- <li>
				<a href="#" class="dropdown-toggle">
					<i class='bx bxs-shopping-bag-alt'></i>
					<span class="text">Brands</span>
					<i class='bx bx-chevron-down'></i>
				</a>
				<ul class="dropdown-menu">
					<li><a href="brand.php">Add Brand</a></li>
					<li><a href="managebrand.php">Manage Brand</a></li>
				</ul>
			</li> -->
			<li>
				<a href="#" class="dropdown-toggle">
					<i class='bx bxs-car'></i>
					<span class="text">Cars</span>
					<i class='bx bx-chevron-down'></i>
				</a>
				<ul class="dropdown-menu">
					<li><a href="postcar.php">Post Cars</a></li>
					<li><a href="managecar.php">Manage cars</a></li>
				</ul>
			</li>

            <li>
				<a href="calendar.php">
					<i class='bx bxs-calendar' ></i>
					<span class="text">Available Cars</span>
				</a>
			</li>
			
			<li>
				<a href="managebook.php">
					<i class='bx bxs-book' ></i>
					<span class="text">Manage Bookings</span>
				</a>
			</li>
			<li >
				<a href="managereview.php">
					<i class='bx bxs-message' ></i>
					<span class="text">Manage feedback</span>
				</a>
			</li>

			<li>
				<a href="user.php">
					<i class='bx bxs-report' ></i>
					<span class="text">Reports</span>
				</a>
			</li>
			
		</ul>
		<ul class="side-menu">
			<li>
				<a href="#" class="logout">
					<i class='bx bxs-log-out-circle' ></i>
					<span class="text">Logout</span>
				</a>
			</li>
		</ul>
	</section>
	<!-- SIDEBAR -->

    <section id="content">
        <!-- NAVBAR -->
		<nav>
			<i class='bx bx-menu' ></i>
			<a href="#" class="nav-link">Categories</a>
			<a href="#" class="notification">
				<i class='bx bxs-bell' ></i>
				<span class="num">8</span>
			</a>
			<a href="#" class="profile">
				<img src="../login/image/user.png">
			</a>
		</nav>
        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Manage Booking</h1>
                    <ul class="breadcrumb">
                        <li>
                            <a href="managebook.php">manage book</a>
                        </li>
                        <li><i class='bx bx-chevron-right' ></i></li>
                        <li>
                            <a class="active" href="#">Booking Details</a>
                        </li>
                    </ul>
                </div>

                <div class="brands-box">

                    <div class="Details">
                        <h2>Renter Details</h2>
                        <h3>Renter Name: </h3>
                        <h3>Email: </h3>
                        <h3>Contact: </h3>
                        <h3>Driver's Lisence: </h3>
                        <hr>

                        <h2>Car Details</h2>
                        <h3>Car Image</h3>
                        <h3>Car Name: </h3>
                        <h3>Car Model: </h3>
                        <hr>

                        <h2>Date</h2>
                        <div class="date">
                            <h3>Pick-up Date: </h3>
                            <h3>Pick-up Time: </h3>
                            <h3>Drop off Date: </h3>
                            <h3>Drop off Time: </h3>
                        </div>
                        <hr>
                        <button>Accept Booking</button>
                        <button>Accept Cancellation</button>
                        
                    </div>
                </div>
            </div>
        </main>
        
    </section>
    
    <script src="script.js"></script>
</body>
</html>