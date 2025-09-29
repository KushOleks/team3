<!DOCTYPE html>
<html lang="uk">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="utf-8">
        <title>Сайт 302 групи ФМІ ЧНУ</title>
        <meta name="theme-color" content="#26A69A">
        <meta name="google" value="notranslate">
        <meta property="og:title" content="Сайт 302 групи ЧНУ" />
        <meta property="og:description" content="302 група ЧНУ - новини, домашні завдання, розклад" />
        <meta property="og:image" content="https://sasha.com.cv.ua/img/og-image.jpg" />
        <meta property="og:image:width" content="480" />
        <meta property="og:image:height" content="360" />
        <meta property="og:url" content="https://sasha.com.cv.ua/" />
        <link rel="shortcut icon" href="/img/favicon.png" type="image/x-icon">
        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <script src="/inc/script.js?v=<?=time()?>"></script>
        <script src="/inc/jquery.cookie.js"></script>
        <link rel="stylesheet" href="light-mode.css" id="theme">
        <link rel="stylesheet" href="/css/style.css?v=<?=time()?>">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700,700i,900,900i&amp;subset=cyrillic,cyrillic-ext,latin-ext" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    </head>
    <body class="all">
        <header>
            <div>
                Сайт 302 групи ФМІ ЧНУ
            </div>
        </header>
        <nav>
            <?
                $current_nav='all';
                if(isset($_COOKIE['nav_c'])){
                    $current_nav=$_COOKIE['nav_c'];
                }
            ?>
            <a href="#"<? if($current_nav=='all') echo ' class="active"' ?> data-val="all">Все</a>
            <a href="#"<? if($current_nav=='news') echo ' class="active"' ?> data-val="news">Новини</a>
            <a href="#"<? if($current_nav=='hw') echo ' class="active"' ?> data-val="hw">Домашні завдання</a>
            <a href="#"<? if($current_nav=='sch') echo ' class="active"' ?> data-val="sch">Розклад</a>
        </nav>
        <main>
            <? /* -------------------------------------------------------- */ ?>
            <?

                include 'inc/funcs.php';

                // перервіряємо змінну з адресного рядка
                $pd_found=0;
                if( isset($_REQUEST['pd']) ){
                    $pd_found=$_REQUEST['pd']+0;
                    if( date('w',$pd_found)==1 ){
                        // тут можна перевіряти чи цей понеділок дуже далеко
                    }else{
                        $pd_found=0;
                    }
                }

                // коментар
                if($pd_found){
                    $start_day=$pd_found;
                }else{
                    $start_day=strtotime('today')+3600*3;
                }

                // зберігаємо всі розклади
                $schedules=array();
                if ($handle = opendir('data/schedule')) {
                    while (false !== ($entry = readdir($handle))) {
                        $this_file_name=strtok($entry,'.');
                        $this_file_ext=strtok('.');
                        if(
                            is_file('data/schedule/'.$entry)
                            &&
                            $this_file_ext=='php'
                        ){
                            $schedule_begin='';
                            $schedule_end='';
                            if(isset($pare_weeks_eque)) unset($pare_weeks_eque);
                            include 'data/schedule/'.$entry;
                            if( $schedule_begin && $schedule_end && isset($pare_weeks_eque) ){
                                $schedules[$this_file_name]=array(
                                    'schedule_begin' => strtotime($schedule_begin),
                                    'schedule_end' => strtotime($schedule_end),
                                    'pare_weeks_eque' => $pare_weeks_eque,
                                );
                            }
                        }
                    }
                    closedir($handle);
                }

                // $start_day

                // формуємо і виводимо посилання на попередні дні
                $for_link=$start_day-86400;
                $do_while=true;
                while($do_while){
                    if( date('w',$for_link)==1 ){
                        $do_while=false;
                    }else{
                        $for_link=$for_link-86400;
                    }
                }
                echo '<div class="pagination">';
                    echo '<a href="/?pd='.$for_link.'">попередні дні</a>';
                    if($pd_found){
                        echo '<a href="/">показати сьогодні</a>';
                    }
                echo '</div>';

                // вивід днів
                for($i=0;$i<7;$i++){

                    $this_day=$start_day+86400*$i;

                    echo '<div>';

                        // -----------------------------------------------------

                        echo '<div class="day">';
                            if( date('d.m.Y',$this_day)==date('d.m.Y',strtotime('today')+3600*3) ){
                                echo '<b>Сьогодні</b>';
                            }elseif( date('d.m.Y',$this_day)==date('d.m.Y',strtotime('today')+3600*27) ){
                                echo '<b class="tm">Завтра</b>';
                            }
                            echo '<font>';
                                echo show_week_day( date('w',$this_day) );
                            echo '</font>';
                            echo '<span>';
                                echo date('d',$this_day);
                                echo ' '.show_month( date('n',$this_day) );
                                echo ' '.date('Y',$this_day);
                            echo '</span>';
                        echo '</div>';

                        // -----------------------------------------------------

                        $DAY_EMPTY=true;

                        $SCHEDULE_FOUND=false;
                        foreach($schedules as $k=>$v){
                            if(
                                $v['schedule_begin'] <= $this_day &&
                                $v['schedule_end'] >= $this_day
                            ){
                                $SCHEDULE_FOUND=$k;
                                break;
                            }
                        }
                        $this_schedule='';
                        if($SCHEDULE_FOUND){
                            $this_nb_week=date('W',$this_day);
                            if($schedules[$SCHEDULE_FOUND]['pare_weeks_eque']){
                                if(even(date('W',$this_day))){
                                    $this_schedule='2week';
                                }else{
                                    $this_schedule='1week';
                                }
                            }else{
                                if(even(date('W',$this_day))){
                                    $this_schedule='1week';
                                }else{
                                    $this_schedule='2week';
                                }
                            }
                        }
                        if($this_schedule){


                            if(!isset($schedules[$SCHEDULE_FOUND][$this_schedule])){
                                get_schedule($SCHEDULE_FOUND,$this_schedule);
                            }

                            if(isset($schedules[$SCHEDULE_FOUND][$this_schedule])){
                                $this_day_day_nb=date('w',$this_day);
                                if(isset($schedules[$SCHEDULE_FOUND][$this_schedule][$this_day_day_nb])){
                                    $temp_sch=$schedules[$SCHEDULE_FOUND][$this_schedule][$this_day_day_nb];
                                    if(count($temp_sch)){
                                        $DAY_EMPTY=false;
                                        foreach($temp_sch as $nb=>$para){
                                            echo '<div class="lesson">';
                                                echo '<p>'.$nb.' пара</p>';
                                                echo '<b>'.$para[0].'</b>';
                                                echo '<em>'.show_bells_schedule($nb).' ('.trim($para[1]).')</em>';
                                                $dz_expected_file='data/homework/'.date('Y/n/j',$this_day).'/'.$nb;
                                                if(file_exists($dz_expected_file)){
                                                    $temp=file_get_contents($dz_expected_file);
                                                    if(trim($temp)){
                                                        echo '<div>';
                                                            echo '<p>Домашнє завдання<i>expand_more</i></p>';
                                                            echo '<div>';
                                                                echo str_replace("\n",'<br>',$temp);
                                                            echo '</div>';
                                                        echo '</div>';
                                                    }
                                                }
                                            echo '</div>';
                                        }
                                    }
                                }
                            }
                        }

                        $blog_found=false;
                        $blog_expected_folder='data/homework/'.date('Y/n/j',$this_day);

                        $blogs=array();
                        if ($handle = opendir($blog_expected_folder)) {
                            while (false !== ($entry = readdir($handle))) {
                                if(
                                    is_dir($blog_expected_folder.'/'.$entry) &&
                                    $entry!='.' && $entry!='..'
                                ){
                                    $blogs[]=$entry;
                                }
                            }
                            closedir($handle);
                        }

                        sort($blogs);

                        foreach($blogs as $entry){
                            $this_blog_folder=$blog_expected_folder.'/'.$entry;
                            if( file_exists($this_blog_folder.'/data.txt') ){
                                if(!$blog_found){
                                    $blog_found=true;
                                    echo '<div class="news">';
                                }
                                $blog=file($this_blog_folder.'/data.txt');
                                $DAY_EMPTY=false;
                                echo '<div>';
                                    if( file_exists($this_blog_folder.'/1.jpg') ){
                                        echo '<p>';
                                            echo '<img src="/'.$this_blog_folder.'/1.jpg">';
                                        echo '</p>';
                                    }elseif( file_exists($this_blog_folder.'/1.jpeg') ){
                                        echo '<p>';
                                            echo '<img src="/'.$this_blog_folder.'/1.jpeg">';
                                        echo '</p>';
                                    }elseif( file_exists($this_blog_folder.'/1.png') ){
                                        echo '<p>';
                                            echo '<img src="/'.$this_blog_folder.'/1.png">';
                                        echo '</p>';
                                    }else{
                                        echo '<p class="no-image">';
                                            echo '<img src="/img/no-image.jpg">';
                                        echo '</p>';
                                    }
                                    echo '<div>';
                                        $blog_text_found=false;
                                        foreach($blog as $k=>$v){
                                            if(!$k){
                                                echo '<h3>'.$v.'</h3>';
                                            }elseif($k==1){
                                                echo '<p>'.$v.'</p>';
                                            }else{
                                                if(!$blog_text_found){
                                                    $blog_text_found=true;
                                                    echo '<b><i>expand_more</i>детальніше</b>';
                                                    echo '<div class="blog_text">';
                                                }
                                                echo str_replace("\n",'<br>',$v);
                                            }
                                        }
                                        if($blog_text_found){
                                            echo '</div>';
                                        }
                                    echo '</div>';
                                echo '</div>';
                            }
                        }
                        if($blog_found){
                            echo '</div>';
                        }

                        // -----------------------------------------------------

                        if($DAY_EMPTY){
                            echo '<div class="empty_day">';
                                echo 'відсутні події';
                            echo '</div>';
                        }

                        // -----------------------------------------------------

                    echo '</div>';

                }

                // нема новин
                echo '<div class="empty_content" id="news_empty_content">';
                    echo '<i>hide_image</i>';
                    echo '<span>';
                        echo 'За період з '.date('d.m.Y',$start_day).' по '.date('d.m.Y',$start_day+86400*6).' новини відсутні.';
                    echo '</span>';
                echo '</div>';

                // нема д/з
                echo '<div class="empty_content" id="hw_empty_content">';
                    echo '<i>extension_off</i>';
                    echo '<span>';
                        echo 'За період з '.date('d.m.Y',$start_day).' по '.date('d.m.Y',$start_day+86400*6).' домашні завдання відсутні.';
                    echo '</span>';
                echo '</div>';

                if(!$pd_found){
                    $for_link=$start_day+86400*3;
                }else{
                    $for_link=$start_day+86400;
                }
                $do_while=true;
                while($do_while){
                    if( date('w',$for_link)==1 ){
                        $do_while=false;
                    }else{
                        $for_link=$for_link+86400;
                    }
                }
                echo '<div class="pagination">';
                    echo '<a href="/?pd='.$for_link.'">показати далі</a>';
                echo '</div>';
            ?>
        </main>
        <footer>
            <a href="mailto:kushnirchuk.oleksandr@chnu.edu.ua">kushnirchuk.oleksandr@chnu.edu.ua</a>
            <p>Адміністрація сайту не несе відповідальності за оцінки студентів, за вчасність написання домашніх завдань та новин </p>
        </footer>
    <?
    // <div class="wrapper">
    // <h1>DM(beta 0.1)</h1>
    // <button class="switchMode" id="switchMode">Switch</button>
    // <div class="tabs">
    // <button class="tab newsTab">Новини</button>
    // <button class="tab assignmentsTab">Домашні завдання</button>
    // <button class="tab scheduleTab">Розклад</button>
    // </div>
    // </div>
    ?>
    </body>
</html>
