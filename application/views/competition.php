<?php require_once 'seourlgen.php';
	  require_once 'urlGenHelper.php';  
	  require_once 'Team.php';
      require_once 'Player.php';
	  $urlGenHelper = new UrlGenHelper();
	  $urlGen = new SeoUrlGen();
	  $session = new Zend_Session_Namespace('userSession');
?>
<?php require_once 'Common.php'; $common = new Common();?>
<?php
    $config = Zend_Registry::get ( 'config' );
    $server = $config->server->host;
    $offset = $config->time->offset->daylight;
 ?>

<script src="<?php echo Zend_Registry::get("contextPath"); ?>/public/scripts/jquery.charcounter.js" type="text/javascript"></script>
<script type="text/JavaScript">

jQuery(document).ready(function() {
    jQuery("#commentGoalShoutId").charCounter(400); 

    jQuery("span[id^='feature_matchDate']").each(function() {
		  var date_time = jQuery(this).text();
		  var date = new Date(date_time);
		  var date_time_os = calcTimeOffset(date,<?php echo $offset;?>);
		  jQuery(this).text(formatDate(date_time_os, 'EE, MMM dd,yyyy'));
	});
	
	jQuery("span[id^='feature_matchHour']").each(function() {
		  var date_time = jQuery(this).text();
		  var date = new Date(date_time);
		  var date_time_os = calcTimeOffset(date,<?php echo $offset;?>);
		 jQuery(this).text(formatDate(date_time_os, 'HH:mm'));
	});


        //setup tabs - featured players and teams
        jQuery('#menu6content,#menu7content').hide();
        jQuery('#menu6content').show();
        jQuery('#menu6').addClass('active');
        jQuery('#featTab ul li').click(function(){
           // var thisClass = this.className;
            jQuery('#menu6content,#menu7content').hide();
            tab_id = jQuery(this).attr('id');
            //show div content
            jQuery('#' + tab_id + 'content').show();
            //show see more header link
            jQuery('#menu6_more,#menu7_more').hide();
            jQuery('#' + tab_id + '_more').show();
            jQuery('#menu6,#menu7').removeClass('active');
            jQuery(this).addClass('active');
       });

        //Setup Tabs - Featured Matches
       jQuery('#menu4content,#menu5content').hide();
       <?php if($this->nextMatch != null) {?>
            jQuery('#menu4content').show();
            jQuery('#menu4').addClass('active');
       <?php } else { ?>
            jQuery('#menu5content').show();
            jQuery('#menu5').addClass('active');
       <?php }  ?>
       jQuery('#matchTab ul li').click(function(){
            jQuery('#menu4content,#menu5content').hide();
            tab_id = jQuery(this).attr('id');
            //show div content
            jQuery('#' + tab_id + 'content').show();
            //show see more header link
            jQuery('#menu4_more,#menu5_more').hide();
            jQuery('#' + tab_id + '_more').show();
            jQuery('#menu4,#menu5').removeClass('active');
            jQuery(this).addClass('active');
        });
        
      //Setup Tabs - Head to Head
       jQuery('#menu1content,#menu2content').hide();
       jQuery('#menu1content').show();
       jQuery('#menu1').addClass('active');
        jQuery('#h2hTab ul li').click(function(){
            jQuery('#menu1content,#menu2content').hide();
            tab_id = jQuery(this).attr('id');
            //show div content
            jQuery('#' + tab_id + 'content').show();
            jQuery('#menu1,#menu2').removeClass('active');
            jQuery(this).addClass('active');
        
        });

        //Competition's Stats - Goals, Yellow Cards, Red Cards
        jQuery('#menu23content,#menu24content,#menu25content').hide();
        jQuery('#menu23content').show();
        jQuery('#statsdropdowncompetition').change(function() {
             jQuery('#menu23content,#menu24content,#menu25content').hide();
             tab_id = jQuery(this).val();
             //show div content
             jQuery('#' + tab_id + 'content').show();
        });
       
        
		//Setup Tabs - Competition's Media
        jQuery('#menu14content,#menu15content').hide();
        jQuery('#menu14content').show();
        jQuery('#menu14').addClass('active');
        jQuery('#mediaTabs ul li').click(function(){
     
             jQuery('#menu14content,#menu15content').hide();
             tab_id = jQuery(this).attr('id');
             //show div content
             jQuery('#' + tab_id + 'content').show();
             jQuery('#menu14,#menu15').removeClass('active');
             jQuery(this).addClass('active');
         
         });

         

        //Setup Tabs - WorldcupStats
       jQuery('#menu8content,#menu9content,#menu10content,#menu11content,#menu12content,#menu13content,#menu21content,#menu22content').hide();
       jQuery('#menu9content').show(); // goals default view
       jQuery('#menu9').addClass('active');
       jQuery('#statTab ul li').click(function(){
           
            jQuery('#menu8content,#menu9content,#menu10content,#menu11content,#menu12content,#menu13content,#menu21content,#menu22content').hide();
            tab_id = jQuery(this).attr('id');
            //show div content
            jQuery('#' + tab_id + 'content').show();

            //hide divs for wc 2010
            if (tab_id == 'menu9') {
               jQuery('#alltime,#wc2010').hide();
               jQuery('#wc2010').show();
            } else {
               jQuery('#alltime,#wc2010').hide();
               jQuery('#alltime').show();          
            }
            jQuery('#menu8,#menu9').removeClass('active');
            jQuery(this).addClass('active');

        });


        // Other Stat Divs    
        jQuery('#statsdropdown').change(function() {
              jQuery('#menu8content,#menu9content,#menu10content,#menu11content,#menu12content,#menu13content,#menu21content,#menu22content').hide();
              tab_id = jQuery(this).val();
              //show div content
              jQuery('#' + tab_id + 'content').show();
         });

         jQuery('#statsdropdownwc2010').change(function() {
              jQuery('#menu8content,#menu9content,#menu10content,#menu11content,#menu12content,#menu13content,#menu21content,#menu22content').hide();
              tab_id = jQuery(this).val();
              //show div content
              jQuery('#' + tab_id + 'content').show();
         });



        callRandonProfiles();
        <?php if($this->leagueId == 72) { ?>
        callFeaturedNews();
        <?php } ?>



	jQuery('#addtofavoritecompetitiontrigger').click(function(){
		 addremovefavoritecompetition('add');
	});
	 jQuery('#removefromfavoritecompetitiontrigger').click(function(){
		 addremovefavoritecompetition('remove');
	});


    var seasonId = '<?php echo $this->seasonId; ?>';
    var activeSeason = '<?php echo $this->seasonActive; ?>';
    var archivedRoundActive = '<?php echo $this->roundId; ?>';
	  var timezone = calculate_time_zone();
	  
    jQuery('#scoreboardResult').html("<div class='ajaxload widget'></div>");

    if(seasonId == activeSeason){
        //current season matches;
        var urlBase = '<?php echo Zend_Registry::get("contextPath"); ?>/scoreboard/showmatchesbycountry/timezone/'+timezone;
        //load the first list by default in scores    
        
        //url = urlBase + 'today';
        //jQuery('#scoreboardResult').load(urlBase ,{countryid :'<php echo $this->countryId;?>@last' , leagueid:'php //echo $this->leagueId;?>' } );
        
        loadScoreBoardByTimeFrame('l7', urlBase , 'last' ,'scoreboardResult' ,'ScoresDateFilter','<?php echo $this->countryId;?>@last','<?php echo $this->leagueId;?>');
        
    } else {
        //archived season matches;
        var page = 'scores';
        var pagecoming = 'comppage'
        var url = '<?php echo Zend_Registry::get("contextPath"); ?>/scoreboard/showfullmatchesbyseason/roundid/'+archivedRoundActive+'/type/'+page+'/page/'+pagecoming;
       
       jQuery('#schedulesTabLi').hide();
       jQuery('#ScoresDateFilter').hide();
       jQuery('#scoreboardResult').load(url);
    }

	//Click event for main tabs scores and schedules
    jQuery('#scoresTab').click(function() {
		loadScoresTab('scoresTabLi', urlBase, 'week', 'scoreboardResult' , 'ScoresDateFilter' ,'l7' , 'tab_2' , '<?php echo $this->countryId;?>@today','<?php echo $this->leagueId;?>');
	 	jQuery('#tab_3').hide();
	});

    jQuery('#schedulesTab').click(function() {
		loadScoresTab('schedulesTabLi', urlBase, 'week', 'scoreboardResult2' , 'SchedulesDateFilter' ,'n7' , 'tab_3' , '<?php echo $this->countryId;?>@<?php echo $this->leagueId;?>' , '<?php echo $this->leagueId;?>');
	 	jQuery('#tab_2').hide();
	});

    //scores by time frame today, last 3, last 7
    jQuery('#today').click(function(){
		loadScoreBoardByTimeFrame(this.id, urlBase , 'today','scoreboardResult' ,'ScoresDateFilter','<?php echo $this->countryId;?>@<?php echo $this->leagueId;?>','<?php echo $this->leagueId;?>');	
	 });

    jQuery('#l3').click(function(){
		loadScoreBoardByTimeFrame(this.id, urlBase , '-3' ,'scoreboardResult' ,'ScoresDateFilter','<?php echo $this->countryId;?>@-3','<?php echo $this->leagueId;?>');			
     });

    jQuery('#l7').click(function(){
		loadScoreBoardByTimeFrame(this.id, urlBase , 'last' ,'scoreboardResult' ,'ScoresDateFilter','<?php echo $this->countryId;?>@last','<?php echo $this->leagueId;?>');			
     });
     
     //schedules by time frame today, last 3, last 7
	jQuery('#tomorrow').click(function(){			
		loadScoreBoardByTimeFrame(this.id, urlBase , 'tomorrow' ,'scoreboardResult2' ,'SchedulesDateFilter','<?php echo $this->countryId;?>@<?php echo $this->leagueId;?>','<?php echo $this->leagueId;?>');			
     });
	jQuery('#n3').click(function(){			
		loadScoreBoardByTimeFrame(this.id, urlBase , '3' ,'scoreboardResult2' ,'SchedulesDateFilter','<?php echo $this->countryId;?>@<?php echo $this->leagueId;?>','<?php echo $this->leagueId;?>');			
     });
	jQuery('#n7').click(function(){			
		loadScoreBoardByTimeFrame(this.id, urlBase , 'week' ,'scoreboardResult2' ,'SchedulesDateFilter' ,'<?php echo $this->countryId;?>@<?php echo $this->leagueId;?>','<?php echo $this->leagueId;?>');			
     });


	showHideDivBox('competitionsScoresId','competitionsScoresBodyId');


	function loadScoresTab(tabid, url, date, container , filter ,defaultsearchid , tabtoshow , countryid , leagueId){
	
		var initDateTime = formatDate(getCurrentInitTime(+2.0),'yyyy-MM-dd HH:mm:ss');
		var endDateTime = formatDate(getCurrentEndTime(+2.0),'yyyy-MM-dd HH:mm:ss');
		jQuery('li.Selected').removeClass('Selected');
	    jQuery('#'+tabid).addClass('Selected');
	    jQuery('#'+ filter+' a').removeClass('filterSelected');
	 	jQuery('#'+defaultsearchid).addClass('filterSelected');
	    jQuery('#'+container).html('');
	    jQuery('#'+container).html("<div class='ajaxload widget'></div>");
		jQuery('#'+tabtoshow).show();
		jQuery('#'+container).load(url ,{countryid :countryid , date:date , leagueid : leagueId, initDateTime : initDateTime, endDateTime : endDateTime} );
		
	}
	function loadScoreBoardByTimeFrame(id, url, date, container , filter ,countryid ,leagueid)
	{
		var initDateTime = formatDate(getCurrentInitTime(+2.0),'yyyy-MM-dd HH:mm:ss');
		var endDateTime = formatDate(getCurrentEndTime(+2.0),'yyyy-MM-dd HH:mm:ss');
		 jQuery('#'+ filter+' a').removeClass('filterSelected');
	 	 jQuery('#' + id).addClass('filterSelected');
		 jQuery('#' + container).html("<div class='ajaxload widget'></div>"); 			
		 jQuery('#' + container).load(url , {date : date , countryid :countryid , leagueid : leagueid, initDateTime : initDateTime, endDateTime : endDateTime});
	} 
	
		
	function showMatchesByTimeFrame(target , button  , select, url){
	
	        jQuery('#' +button).click(function(){ //se ejecuta con el evento onCLick
	                var $this = jQuery('#'+select);
	                var dateId = $this.val();
	                jQuery('#' +target).html('Loading...');
	                jQuery('#' +target).load(url ,{countryid :'<?php echo $this->countryId;?>@<?php echo $this->leagueId;?>' ,date : dateId ,leagueId:'<?php echo $this->leagueId;?>' } );
	          });
	}




	jQuery('#teamplayerselectida').change(function(){
    	var teamid = jQuery("#teamplayerselectida").val();
    	populateCombo('playerselectida', teamid);
    }); 

	jQuery('#teamplayerselectidb').change(function(){
    	var teamid = jQuery("#teamplayerselectidb").val();
    	populateCombo('playerselectidb', teamid);
    });
    
	//Click the button event! Head to head WC Teams
	jQuery('#compTeamsWC').click(function(){
		validateHead2HeadSelectTeams();
		var teama = jQuery("#teamselectida").val();
		var teamb = jQuery("#teamselectidb").val();
		if(teama != '0' && teamb != '0' && teama != teamb) {
			var url = '<?php echo Zend_Registry::get("contextPath"); ?>/competitions/findhead2headmatches/teama/'+teama+'/teamb/'+teamb+'/competitionid/72';
			top.location.href = url;			
		}
	});	

	//Click the button event! Head to head WC Players
	jQuery('#compPlayersWC').click(function(){
		validateHead2HeadSelectPlayers();
		var teama = jQuery("#teamplayerselectida").val();
		var teamb = jQuery("#teamplayerselectidb").val();
		var playera = jQuery("#playerselectida").val();
		var playerb = jQuery("#playerselectidb").val();
		if(teama != '0' && teamb != '0' &&
				playera != '0' && playerb != '0' && playera != playerb) {
			var url = '<?php echo Zend_Registry::get("contextPath"); ?>/competitions/findhead2headplayers/teama/'+teama+'/teamb/'+teamb+'/playera/'+playera+'/playerb/'+playerb+'/competitionid/72';
			top.location.href = url;			
		}
	});	
		 	
});

function validateHead2HeadSelectTeams() {
	var teama = jQuery("#teamselectida").val();
	var teamb = jQuery("#teamselectidb").val();
	if(teama == teamb) {
		jQuery('#errorTeamsWC').removeClass('ErrorMessageIndividual').addClass('ErrorMessageIndividualDisplay');					
	} else {
		jQuery('#errorTeamsWC').removeClass('ErrorMessageIndividualDisplay').addClass('ErrorMessageIndividual');
	}	
}

function validateHead2HeadSelectPlayers() {
	var playera = jQuery("#playerselectida").val();
	var playerb = jQuery("#playerselectidb").val();
	if(playera == playerb) {
		jQuery('#errorPlayersWC').removeClass('ErrorMessageIndividual').addClass('ErrorMessageIndividualDisplay');					
	} else {
		jQuery('#errorPlayersWC').removeClass('ErrorMessageIndividualDisplay').addClass('ErrorMessageIndividual');
	}	
}

function callRandonProfiles()
{
    jQuery.ajax({
        method: 'get',
        url : '<?php echo Zend_Registry::get("contextPath"); ?>/profile/showprofilesrandom/competitionId/<?php echo $this->leagueId;?>',
        dataType : 'text',
        success: function (text) {
            jQuery('#randomprofiles').html(text);
        }
    });

}



function callFeaturedNews(){

    jQuery('#FeaturedNewsList').html("<div class='ajaxload widget'></div>");
	jQuery.ajax({
        method: 'get',
        url : '<?php echo Zend_Registry::get("contextPath"); ?>/news/homefeaturednews/numfeeds/5/cat/<?php echo urlencode("world cup");?>',
        dataType : 'text',
        success: function (text) {
                jQuery('#FeaturedNewsList').html(text);
		}
     });

}



	
function addremovefavoritecompetition(type){

         jQuery('#modalBodyId').show();
         jQuery('#modalBodyResponseId').hide();
         jQuery('#acceptFavoriteModalButtonId').show();
         jQuery('#cancelFavoriteModalButtonId').attr('value','Cancel');
         if(type == 'add'){
                jQuery('#modalFavoriteTitleId').html('Add <?php echo $this->compName;?> as your favorite competition?');
         }else if(type == 'remove'){
                 jQuery('#modalFavoriteTitleId').html('Remove <?php echo $this->compName;?> as your favorite competition?');
         }	 	 
         jQuery('#dataText1').html('<?php echo $this->compName;?>');
         jQuery('#title1Id').html('Country:');
         jQuery('#dataText2').html("<?php echo $this->countryName; ?>");
         jQuery('#dataText2').attr('href','<?php echo $urlGen->getShowDomesticCompetitionsByCountryUrl($this->countryName,$this->countryId, true); ?>');

         if(type == 'add'){
        	 	jQuery('#checkBoxUpdates').show();
        	 	jQuery('#title5Id').html('<?php echo $this->compName;?>');
                jQuery('#addFavoriteModal').jqm({trigger: '#addtofavoritecompetitiontrigger', onHide: closeModal });
         }else if(type == 'remove'){
        	     jQuery('#checkBoxUpdates').hide();
                 jQuery('#addFavoriteModal').jqm({trigger: '#removefromfavoritecompetitiontrigger', onHide: closeModal });
         }
         jQuery('#addFavoriteModal').jqmShow();

         var favoriteImage = null;

        <?php
         $config = Zend_Registry::get ( 'config' );
         $path_comp_logos = $config->path->images->complogos . $this->leagueId.".gif" ;

         if (file_exists($path_comp_logos)){  ?>
       			favoriteImage = '<?php echo Zend_Registry::get("contextPath"); ?>/utility/imageCrop?w=60&h=60&zc=1&src=<?php echo Zend_Registry::get("contextPath"); ?><?php echo $this->root_crop; ?>/competitionlogos/<?php  echo $this->leagueId .'.gif'?>';
		<?php } else {  ?>
				favoriteImage = '<?php echo Zend_Registry::get("contextPath"); ?>/utility/imageCrop?w=60&h=60&zc=1&src=<?php echo Zend_Registry::get("contextPath"); ?><?php echo $this->root_crop; ?>/LeagueText120.gif';
    	<?php  } ?>

		 
		 jQuery('#favoriteImageSrcId').attr('src',favoriteImage);

		 var leagueId = '<?php echo $this->leagueId; ?>*<?php echo $this->countryId; ?>';	
		
		 var url = null;
		 var htmlResponse = null;
		 if(type == 'add'){ 
			 url = '<?php echo Zend_Registry::get("contextPath"); ?>/competitions/addfavorite' ;
			 htmlResponse = 'Competition <?php echo $this->compName;?> has been added to your favorite competitions.';
		 }else if(type == 'remove'){
			 url = '<?php echo Zend_Registry::get("contextPath"); ?>/competitions/removefavorite' ;
			 htmlResponse = 'Competition <?php echo $this->compName;?> has been removed from your favorite competitions.';
		 }	 	 
		 


		 jQuery("#acceptFavoriteModalButtonId").unbind();
		 jQuery('#acceptFavoriteModalButtonId').click(function(){
			 if(type == 'add'){ 
				 var updatesCheck = "0";
	  			 if(jQuery("#updatesCheck").is(':checked')){
	  				 updatesCheck = "1";
	  			 }
			 }		 
		 jQuery.ajax({
				type: 'POST',
				url :  url,
				data : ({leagueId:leagueId ,updatesCheck : updatesCheck}),
				success: function(data){
					jQuery('#modalBodyResponseId').html(htmlResponse);
					jQuery('#modalBodyId').hide();
					jQuery('#modalBodyResponseId').show();
					jQuery('#acceptFavoriteModalButtonId').hide();
					jQuery('#cancelFavoriteModalButtonId').attr('value','Close');
					if(type == 'add'){ 
						jQuery('#favorite').hide();
					 	jQuery('#remove').show()
					}else if(type == 'remove'){
				 		jQuery('#remove').hide();
				 		jQuery('#favorite').show();
				 	}		
				 	 
					jQuery('#addFavoriteModal').animate({opacity: '+=0'}, 2500).jqmHide();
				}	
			})
			
		 });	
	}	


function addGoalShout(){

    var commentText = jQuery('#commentGoalShoutId').val();
    if(jQuery.trim(commentText) == ''){
        jQuery('#comment_formerror').removeClass('ErrorMessageIndividual').addClass('ErrorMessageIndividualDisplay');
        return;
    }
    var url = '<?php echo Zend_Registry::get ( "contextPath" );?>/profile/addgoalshout';

    var commentType = 7;
    var idtocomment = '<?php echo $this->leagueId; ?>';
    var screennametocomment = '<?php echo $session->screenName;?>';
    var countryid = '<?php echo $this->countryId;?>';
    
    jQuery('#goalshoutId').load(url ,{leagueid :idtocomment , countryid : countryid , commentType: commentType , idtocomment : idtocomment ,screennametocomment : screennametocomment , comment : commentText});
    jQuery('#commentGoalShoutId').val('');

}



 function populateCombo(dtarget , id){
	 var url = null;
	 var ajaxload = null;
     url = '<?php echo Zend_Registry::get("contextPath"); ?>/player/showworldcupplayerbyteam/id/'+id;
     ajaxload = 'ajaxloaderTeamPlayer';
	 jQuery('#'+ajaxload).show();
		 jQuery('#'+dtarget).load(url , function(){
			 jQuery('#'+ajaxload).hide();
		 });
 }
 
</script>

<div id="ContentWrapper">
  <p class="flags">
      <span style="background-image: url(<?php echo Zend_Registry::get ( "contextPath" ); ?>/public/images/flags/32x32/<?php echo $this->countryCodeIso; ?>.png);" class="flagtitle">
           <?php if ($this->compFormat == 'International cup') { ?>
              <?php echo $this->regionNameTitle; ?> - <?php echo $this->compName; ?>
              <?php } else { ?>
              <?php echo $this->countryName; ?> - <?php echo $this->compName; ?>
           <?php } ?>
      </span>
  
        <?php if($this->isFavorite == 'false') { ?>
             <?php if($session->email != null){ ?>
              <span id="favorite" class="add">
                  <a id="addtofavoritecompetitiontrigger" href="#">Add to Favorites</a>
              </span>
               <span id="remove" class="remove" style="display:none">
                  <a id="removefromfavoritecompetitiontrigger" href="#">Remove from Favorites</a>
              </span>
             <?php } else {?>
               <span id="favorite" class="add">
                  <a id="addtofavoritecompetitionNonLoggedtrigger" onclick="loginModal()" href="#">Add to Favorites</a>
              </span>
             <?php } ?>
         <?php }else {?>
             <span id="favorite" class="add" style="display:none">
                  <a id="addtofavoritecompetitiontrigger" href="#">Add to Favorites</a>
              </span>
               <span id="remove" class="remove">
                  <a id="removefromfavoritecompetitiontrigger" href="#">Remove from Favorites</a>
              </span>
         <?php }?>
  
      <?php if ($server == 'local') { ?>
      
         <span class="pre">

           <span class="lea">Archive</span>
        
              <select onchange="document.seasondropdown.submit();" id="seasonId" name="seasonId" class="sel">
                     <?php foreach($this->seasonList as $season) { ?>
                          <option value="<?php echo $season["season_id"]; ?>" <?php echo ($this->seasonId == $season["season_id"]?'selected':'')?> ><?php echo $season["title"]; ?></option>
                        <?php }  ?>
              </select>

          </span>
        
   	 <?php } ?>
              
  </p>
  
  <div class="FirstColumn">
		<?php echo $this->render('include/topleftbanner.php')?>
		
        <?php 
            if($session->email != null){
        ?> 
            <div class="img-shadow">
                <div class="WrapperForDropShadow">
                    <?php include 'include/loginbox.php';?>
                </div>
            </div>
      
			
            <?php } else {?>


            <!--Me box Non-authenticated-->
            <div class="img-shadow">
                <div class="WrapperForDropShadow">
                    <?php include 'include/loginNonAuthBox.php';?>
                </div>
            </div>
            
        <?php } ?>

        <div id="leftnav" class="img-shadow">
            <?php echo $this->render('include/navigationCompetitionNew2.php');?>
        </div>
        
   </div><!--/FirstColumn-->
  
   <div class="SecondColumn">
   
    <!--New  Featured  Matches -->
      <div class="prof">
          <p class="mblack">
              <span class="black">Featured Matches</span>
              <span id="menu4_more" class="sm"><a href="<?php echo $urlGen->getTablesUrl("schedules",$this->roundId ,$this->countryName ,$this->seasonTitle ,  $this->compName ,True) ?>">More &raquo;</a></span>
              <span id="menu5_more" class="sm" style="display:none;"><a href="<?php echo $urlGen->getTablesUrl("scores",$this->roundId ,$this->countryName ,$this->seasonTitle ,  $this->compName ,True) ?>">More &raquo;</a></span>          </p>
          <div id="matchTab" class="nxt">
              <ul>
                  <li id="menu4"><a href="javascript:void(0);">Next Match</a></li>
                  <li id="menu5"><a href="javascript:void(0);">Latest Match</a></li>
              </ul>
          </div>
          <div class="nmatch" id="menu4content">
             <?php if($this->nextMatch != null) {?>
                         <p class="kick">
             		Date: <span>
	                    	<span id="feature_matchDate_nxt">
	                    		<?php echo date('Y/m/d H:i:s',strtotime($this->nextMatch['mdate'] ." ". $this->nextMatch['TIME'])) ?> 
	                    	</span>	
                    	</span> <br> 		
             				
                    Kickoff: <span>
                    	<span id="feature_matchHour_nxt">
                    	<?php echo date('Y/m/d H:i:s',strtotime($this->nextMatch['mdate'] ." ". $this->nextMatch['TIME'])) ?> 
                    	</span>
					</span>
                    <br>
                 </p>
   
                  <div class="vss">
                          <p class="tfa">
                          
                              <a href="<?php echo $urlGen->getMatchPageUrl($this->compName, $this->nextMatch["teama"], $this->nextMatch["teamb"], $this->nextMatch["matchid"], true);?>">
                               <?php
                                  $config = Zend_Registry::get ( 'config' );
                                  $path_team_logos = $config->path->images->teamlogos . $this->nextMatch["cteama"].".gif" ;

                                  if (file_exists($path_team_logos))
                                  {  ?>
                                    <img src="<?php echo Zend_Registry::get("contextPath"); ?>/public/images/teamlogos/<?php echo $this->nextMatch["cteama"]?>.gif" alt="<?php echo $this->nextMatch['teama'] ?>"/>

                                  <?php } else {  ?>

                                    <img alt="" src="<?php echo Zend_Registry::get("contextPath"); ?>/public/images/TeamText80.gif" alt="<?php echo $this->nextMatch['teama'] ?>">

                              <?php }   ?>
                              </a>
                              <a href="<?php echo $urlGen->getMatchPageUrl($this->compName, $this->nextMatch["teama"], $this->nextMatch["teamb"], $this->nextMatch["matchid"], true);?>">

                                  <?php echo $this->nextMatch['teama'] ?>
                              </a>
                          </p>
                          
                            <?php if (($this->nextMatch["status"] == 'Playing') OR ($this->nextMatch["status"] == 'Fixture'))  {
                            	  if($this->nextMatch["status"] == 'Playing'){?>	
                                       <p class="vs1" style="width:77px;">
                                        <?php echo $this->nextMatch["fs_team_a"];?> - <?php echo $this->nextMatch["fs_team_b"];?><br>
                                      Playing &nbsp;<?php echo $this->nextMatch["match_minute"];?></p>
                           	 <?php } else { ?>
                              	<p class="vs1">vs</p>
                            	<?php }
                            		}  
                            	 ?>
            
                          <p class="fff">

                              <a href="<?php echo $urlGen->getMatchPageUrl($this->compName, $this->nextMatch["teama"], $this->nextMatch["teamb"], $this->nextMatch["matchid"], true);?>">
                          	<?php
                              $path_team_logos = $config->path->images->teamlogos . $this->nextMatch["cteamb"].".gif" ;

                              if (file_exists($path_team_logos))
                              {  ?>
                                <img src="<?php echo Zend_Registry::get("contextPath"); ?>/public/images/teamlogos/<?php echo $this->nextMatch["cteamb"]?>.gif" alt="<?php echo $this->nextMatch['teamb'] ?>"/>

                              <?php } else {  ?>

                                <img alt="" src="<?php echo Zend_Registry::get("contextPath"); ?>/public/images/TeamText80.gif" alt="<?php echo $this->nextMatch['teamb'] ?>">

                          	<?php }   ?>
                          
                          	</a>
                          	<a href="<?php echo $urlGen->getMatchPageUrl($this->compName, $this->nextMatch["teama"], $this->nextMatch["teamb"], $this->nextMatch["matchid"], true);?>">
                                <?php echo $this->nextMatch['teamb'] ?>
                             </a>
                          
                          </p>
                  </div>
                  <p class="smatch">
                      <a href="<?php echo $urlGen->getMatchPageUrl($this->compName, $this->nextMatch["teama"], $this->nextMatch["teamb"], $this->nextMatch["matchid"], true);?>">
                        <?php if (($this->nextMatch["status"] == 'Playing') OR ($this->nextMatch["status"] == 'Fixture'))  {?>
                          See Match Details &raquo;
                        <?php } else {  ?>
                          See Match Preview &raquo;
                        <?php }  ?>
                      </a>
                  </p>
                  <p class="smatch1">
                      <a class="OrangeLink" href="<?php echo $urlGen->getTablesUrl("schedules",$this->roundId ,$this->countryName ,$this->seasonTitle ,  $this->compName ,True) ?>"><?php echo $this->compName  ?> Match Schedule</a>
                  </p>
                <?php }else {
                  	echo "<br><center><strong>No Matches scheduled yet.</strong></center>";
                  }?>
              
          </div>
          
          <div class="nmatch" id="menu5content">
                  <?php if($this->previousMatch != null) {?>
                   <p class="kick">
                    Date: <span>
	                    	<span id="feature_matchDate_nxt">
	                    		<?php echo date ('l - F j , Y' , strtotime($this->previousMatch['mdate'])) ;?>
	                    	</span>	
                    	</span> <br> 
                    Kickoff: 
                    <span>
                    	<span id="feature_matchHour_nxt">
                    	<?php echo date('Y/m/d H:i:s',strtotime($this->previousMatch['mdate'] ." ". $this->previousMatch['TIME'])) ?> 
                    	</span>
                    </span><br>
                  </p>
                  <div class="vss">
                          <p class="tfa">

                           <a href="<?php echo $urlGen->getClubMasterProfileUrl($this->previousMatch["cteama"], $this->previousMatch["teamaseoname"], True); ?>">
                          <?php
                              $config = Zend_Registry::get ( 'config' );
                              $path_team_logos = $config->path->images->teamlogos . $this->previousMatch["cteama"].".gif" ;

                              if (file_exists($path_team_logos))
                              {  ?>
                                <img src="<?php echo Zend_Registry::get("contextPath"); ?>/public/images/teamlogos/<?php echo $this->previousMatch["cteama"]?>.gif"/>

                              <?php } else {  ?>

                                <img alt="" src="<?php echo Zend_Registry::get("contextPath"); ?>/public/images/TeamText80.gif">

                          <?php }   ?>
                          </a>
                          <a href="<?php echo $urlGen->getMatchPageUrl($this->compName, $this->previousMatch["teama"], $this->previousMatch["teamb"], $this->previousMatch["matchid"], true);?>">
                              <?php echo $this->previousMatch['teama'] ?>
                          </a>
                         
                          
                          </p>
                          
                            
                              <p class="vs1">
                              <?php echo $this->previousMatch["fs_team_a"];?> - <?php echo $this->previousMatch["fs_team_b"];?>
                              <br>Final</p>
                  			                                         
                          
                          <p class="fff">

	                          <a href="<?php echo $urlGen->getMatchPageUrl($this->compName, $this->previousMatch["teama"], $this->previousMatch["teamb"], $this->previousMatch["matchid"], true);?>">
	                          
	                           <?php
	                              
	                              $path_team_logos = $config->path->images->teamlogos . $this->previousMatch["cteamb"].".gif" ;
	
	                              if (file_exists($path_team_logos))
	                              {  ?>
	                                <img src="<?php echo Zend_Registry::get("contextPath"); ?>/public/images/teamlogos/<?php echo $this->previousMatch["cteamb"]?>.gif"/>
	
	                              <?php } else {  ?>
	
	                                <img alt="" src="<?php echo Zend_Registry::get("contextPath"); ?>/public/images/TeamText80.gif">
	
	                          <?php }   ?>
	                          </a>
	                           <a href="<?php echo $urlGen->getClubMasterProfileUrl($this->previousMatch["cteamb"], $this->previousMatch["teambseoname"], True); ?>">
	                           <?php echo $this->previousMatch['teamb'] ?>
	                           </a>
                          
                          </p>
                  </div>
                  <p class="smatch"><a href="<?php echo $urlGen->getMatchPageUrl($this->compName, $this->previousMatch["teama"], $this->previousMatch["teamb"], $this->previousMatch["matchid"], true);?>">See Match Details &raquo;</a></p>

                  <p class="smatch1"><a class="OrangeLink" href="<?php echo $urlGen->getTablesUrl("scores",$this->roundId ,$this->countryName ,$this->seasonTitle ,  $this->compName ,True) ?>"><?php echo $this->compName;?> Match Scores</a></p>
                  <?php }else {
                  	echo "<br><center><strong>No Matches played yet.</strong></center>";
                  }?>
          </div>
      </div>
      
          <!--New  Featured  Teams and Players -->
     <div class="prof">
       	<p class="mblack">
              <span class="black">Featured Teams &amp; Players</span>
              <span id="menu6_more" class="sm"><a href="<?php echo $urlGen->getFeaturedTeamsUrl(TRUE);?>">More &raquo;</a></span>
              <span id="menu7_more" class="sm" style="display:none;"><a href="<?php echo $urlGen->getFeaturedPlayersUrl(TRUE);?>">More &raquo;</a></span>                        
        	</p>
        
          <div id="featTab" class="nxt">
              <ul>
                  <li id="menu6"><a href="javascript:void(0);">Teams</a></li>
                  <li id="menu7"><a href="javascript:void(0);">Players</a></li>
              </ul>
          </div>
          
         <div class="nmatch" id="menu6content">
          <?php
              $config = Zend_Registry::get ( 'config' );
              $teamCounter = 0;
              if(sizeOf($this->featuredTeams) == 0){
              	echo "<br><center><strong>No Data Available.</strong></center>";
              }else {
              
              
              foreach ($this->featuredTeams  as $data) {
               $teamCounter++;
          ?>
          <?php if($teamCounter==1){  ?>
            <div class="imgs">
          <?php }  ?>

              <p class="<?php if($teamCounter==2){ echo "tfa1"; } else { echo "tfa"; } ?>">

                  <a href="<?php echo $urlGen->getClubMasterProfileUrl($data['team_id'],$data['team_seoname'], True); ?>" title="<?php echo $data['team_name'];?>">
                     <?php 
                        $path_team_logos = $config->path->images->teamlogos . $data['team_id'].".gif" ;   
                        if (file_exists($path_team_logos)) { ?> 
                          <img src="<?php echo Zend_Registry::get("contextPath"); ?>/public/images/teamlogos/<?php echo $data['team_id']?>.gif" alt="<?php echo $data['team_name'];?>"/>
                      <?php } else { ?>
                          <img src="<?php echo Zend_Registry::get("contextPath"); ?>/public/images/TeamText80.gif" alt="<?php echo $data['team_name'];?>"/>
                      <?php } ?>
                  </a>
                  <a href="<?php echo $urlGen->getClubMasterProfileUrl($data['team_id'],$data['team_seoname'], True); ?>" title="<?php echo $data['team_name'];?>">
                    <?php echo $data['team_name'];?>
                  </a>
                  
              <?php if ($session->email != null) { ?>
    					<?php if($session->userId == $data['user_id']) { ?>
    					     <a id="btn_team_off_<?php echo $data["team_id"];?>" class="unsubscribe" href="javascript:" onclick="unsubscribeToTeam(<?php echo $data["team_id"];?>, '<?php echo $data['team_name'];?>');">
    							<img src="<?php echo Zend_Registry::get("contextPath"); ?>/public/images/unsubscribe.jpg" alt="Subscribe to <?php echo $data["team_name"];?>'s updates">
    						</a>
    						 <a id="btn_team_on_<?php echo $data["team_id"];?>" class="subscribe ScoresClosed" href="javascript:" onclick="subscribeToTeam(<?php echo $data["team_id"];?>, '<?php echo $data['team_name'];?>');">
    							<img src="<?php echo Zend_Registry::get("contextPath"); ?>/public/images/subscribe.jpg" alt="Subscribe to <?php echo $data["team_name"];?>'s updates">
    						</a>
    					<?php } else { ?>
    						 <a id="btn_team_on_<?php echo $data["team_id"];?>" class="subscribe" href="javascript:" onclick="subscribeToTeam(<?php echo $data["team_id"];?>, '<?php echo $data['team_name'];?>');">
    							<img src="<?php echo Zend_Registry::get("contextPath"); ?>/public/images/subscribe.jpg" alt="Subscribe to <?php echo $data["team_name"];?>'s updates">
    						</a>
    						<a id="btn_team_off_<?php echo $data["team_id"];?>" class="unsubscribe ScoresClosed" href="javascript:" onclick="unsubscribeToTeam(<?php echo $data["team_id"];?>, '<?php echo $data['team_name'];?>');">
    							<img src="<?php echo Zend_Registry::get("contextPath"); ?>/public/images/unsubscribe.jpg" alt="Subscribe to <?php echo $data["team_name"];?>'s updates">
    						</a>
    					<?php } ?>
					
    			   <?php } else { ?>
    				     <a class="subscribe" href="javascript:" onclick="subscribeToTeam(<?php echo $data["team_id"];?>, '<?php echo $data['team_name'];?>');">
    						<img src="<?php echo Zend_Registry::get("contextPath"); ?>/public/images/subscribe.jpg" alt="Subscribe to <?php echo $data["team_name"];?>'s updates">
    					</a>
    			   <?php } ?>	

              </p>

           <?php if($teamCounter==3){ $teamCounter = 0; ?>
              </div>
            <?php } ?>

              <?php } 
              }?>

                <p class="smatch1">
                    <a class="OrangeLink" href="<?php echo $urlGen->getFeaturedTeamsUrl(TRUE);?>">See More Featured Teams</a>
                </p>
            </div>
            
        <div class="nmatch" id="menu7content">
          <?php
              $config = Zend_Registry::get ( 'config' );
              $playerCounter = 0;
              if(sizeOf($this->featuredPlayers) == 0){
              	echo "<br><center><strong>No Data Available.</strong></center>";
              }else {
              foreach ($this->featuredPlayers  as $pp) {
               $playerCounter++;
          ?>
          
              <?php if($playerCounter==1){  ?>
                  <div class="imgs">
               <?php }  ?>

               <p class="<?php if($playerCounter==2){ echo "tfa1"; } else { echo "tfa"; } ?>">
                             
                    <a href="<?php echo $urlGen->getPlayerMasterProfileUrl($pp["player_nickname"], $pp["player_firstname"], $pp["player_lastname"], $pp["player_id"], true ,$pp["player_common_name"]); ?>" title="<?php echo $pp["player_name_short"] ?>">
                           <?php
						            $path_player_photos = $config->path->images->players . $pp["player_id"] .".jpg" ;
						             if (file_exists($path_player_photos)) { 
						          ?>
                                        <img id="player<?php echo $pp["player_id"];?>profileImage" src="<?php echo Zend_Registry::get("contextPath"); ?>/utility/imageCrop?w=80&h=80&zc=1&src=<?php echo Zend_Registry::get("contextPath"); ?><?php echo $this->root_crop; ?>/players/<?php echo $pp["player_id"]; ?>.jpg" alt="<?php echo $pp["player_common_name"];?>"/>
                                        
                                   <?php } else  { ?>
                                        <img id="player<?php echo $pp["player_id"];?>profileImage" src="<?php echo Zend_Registry::get("contextPath"); ?>/public/images/Player1Text80.gif" alt="<?php echo $pp["player_common_name"];?>"/>
                                  <?php } ?>
                      </a>
                      <a href="<?php echo $urlGen->getPlayerMasterProfileUrl($pp["player_nickname"], $pp["player_firstname"], $pp["player_lastname"], $pp["player_id"], true ,$pp["player_common_name"]); ?>" title="<?php echo $pp["player_name_short"] ?>">
                                <?php echo $pp["player_name_short"];?>
                      </a>
                      <?php if ($session->email != null) { ?>
          					<?php if($session->userId == $pp['user_id']) { ?>
                                  <a id="btn_player_off_<?php echo $pp["player_id"];?>" class="unsubscribe" href="javascript:" onclick="unsubscribeToPlayer(<?php echo $pp["player_id"];?>);">
                            				<img src="<?php echo Zend_Registry::get("contextPath"); ?>/public/images/unsubscribe.jpg" alt="Subscribe to <?php echo $pp["player_name_short"];?>'s updates">
                            			</a>
                            			<a id="btn_player_on_<?php echo $pp["player_id"];?>" class="subscribe  ScoresClosed" href="javascript:" onclick="subscribeToPlayer(<?php echo $pp["player_id"];?>);">
                            				<img src="<?php echo Zend_Registry::get("contextPath"); ?>/public/images/subscribe.jpg" alt="Subscribe to <?php echo $pp["player_name_short"];?>'s updates">
                            			</a>
                              <?php } else { ?>
                              	<a id="btn_player_on_<?php echo $pp["player_id"];?>" class="subscribe" href="javascript:" onclick="subscribeToPlayer(<?php echo $pp["player_id"];?>);">
                            				<img src="<?php echo Zend_Registry::get("contextPath"); ?>/public/images/subscribe.jpg" alt="Subscribe to <?php echo $pp["player_name_short"];?>'s updates">
                            			</a>
                            			<a id="btn_player_off_<?php echo $pp["player_id"];?>" class="unsubscribe ScoresClosed" href="javascript:" onclick="unsubscribeToPlayer(<?php echo $pp["player_id"];?>);">
                            				<img src="<?php echo Zend_Registry::get("contextPath"); ?>/public/images/unsubscribe.jpg" alt="Subscribe to <?php echo $pp["player_name_short"];?>'s updates">
                            		</a>
                            <?php }  ?>
					<?php } else { ?>
					    <a id="btn_playerid_<?php echo $pp["player_id"];?>" class="subscribe" href="javascript:" onclick="subscribeToPlayer(<?php echo $pp["player_id"];?>);">
							<img src="<?php echo Zend_Registry::get("contextPath"); ?>/public/images/subscribe.jpg" alt="Subscribe to <?php echo $pp["player_name_short"];?>'s updates">
						</a>
					<?php }  ?>	
					
                </p>

           <?php if($playerCounter==3){ $playerCounter = 0; ?>
              </div>
            <?php } ?>

              <?php }
              } ?>

                <p class="smatch1">
                    <a class="OrangeLink" href="<?php echo $urlGen->getFeaturedPlayersUrl(TRUE);?>">See More Featured Players</a>
                </p>
            </div>
            
        
        </div>
        <!--BEGIN Groups Standings Table --> 
        
        <?php 
              require_once 'Round.php';
	            require_once 'Team.php';
	            $round = new Round();
              $equipo = new Team();	
          ?>
        
  
        <div class="prof2">
        	<p class="mblack">
              <span class="black"><?php echo $this->compName; ?> Table </span>
            </p>
						<div id="leaguetables">
                <?php if ($this->leagueTable != null) { ?> 

     			 				<table id="LeagueTable" width="100%" cellspacing="0" cellpadding="1" border="0">
                    <?php foreach ($this->leagueTable as $tournament ){ ?>
                        <?php $roundRow = $round->fetchRow( 'round_id = ' . $tournament['stage_id'] ); ?>

                                <?php if($roundRow['type'] == 'table') { ?>
                    		    			
                                        <tr class="gheading">
                                        	<td class="ghtd" nowrap colspan="9">
                                                 <?php if($this->compFormat == 'Domestic league') { ?>
                                        	    	<?php  //$titlesplit = explode ( ":", $tournament['league'] ); ?>
                                        	        <?php echo $tournament['league']//$titlesplit[1];?>
                                        	      <?php } else { ?>   
                                        	        <?php  $titlesplit = explode ( ":", $tournament['name'] ); ?>
                                        	        <?php echo $titlesplit[1];?>
                                        	      <?php } ?>     
                                        	</td>
                                        </tr>
                                        
                                        <tr class="lheading" align="left">
                                            <th class="lhtd">#</th>
                                            <th class="lhtd">&nbsp;</th>
                                            <th class="lhtd">Team</th>
                                            <th class="lhtd">MP</th>
                                            <th class="lhtd">W</th>
                                            <th class="lhtd">D</th>
                                            <th class="lhtd">L</th>
                                            <th class="lhtd">+/-</th>
                                            <th class="lhtd">Pts</th>
                                         </tr>
                                         
                                        
                                              <?php $i = 1; 
                                                     foreach ($tournament as $team ) {  ?>
                                                     <?php $teamRow = $equipo->fetchRow( 'team_gs_id = ' . $team['id'] ); ?>
                                                       <?php if($i % 2 == 1) { $style = 'odd'; }else{ $style = 'even';}?>
                                                       
                                                       
                                                          	<tr class="<?php echo $style; ?>">
                                                                <td class="first">
                                                                	<?php echo $team['position'] ?>
                                                                </td>
                                                                 <td class="logo">
                                                                 
                                                                    <?php if($this->compFormat == 'Domestic league') { ?>
                                                                         <?php
                                                    					        $config = Zend_Registry::get('config');
                                                    					        $path_team_logos = $config->path->images->teamlogos . $teamRow['team_id'] .".gif";
                                                    					        if (file_exists($path_team_logos)) {
                                                    					        ?>
                                                    					            <img src="<?php echo Zend_Registry::get("contextPath");?>/utility/imagecrop?w=18&h=18&zc=1&src=<?php echo Zend_Registry::get("contextPath");?><?php echo $this->root_crop;?>/teamlogos/<?php echo $teamRow['team_id']?>.gif" />
                                                    					          <?php  } else { ?>
                                                    					            <img src="<?php echo Zend_Registry::get("contextPath");?>/utility/imagecrop?w=18&h=18&zc=1&src=<?php echo Zend_Registry::get("contextPath");?><?php echo $this->root_crop;?>/TeamText80.gif" />
                                                    					         <?php  } ?>
                                                                       
                                                                    <?php } else {?> <!--International cup --> 
                                                                 
                                                                            <?php 
                                                                                 $country_flag = $teamRow['country_id'] .".png";
                                                                             ?>
                                                                              <img src="<?php echo Zend_Registry::get("contextPath"); ?>/public/images/flags/<?php echo $country_flag; ?>" alt="<?php echo $team['name']; ?>">
                                            	
                        		    		                        <?php } ?>
                                                                 </td>
                                                                <td>
                                                                  	<a href="<?php echo $urlGen->getClubMasterProfileUrl($teamRow['team_id'],$teamRow['team_seoname'], True); ?>">
                                                                       <?php //echo mb_convert_encoding($ranking['name'], "ISO-8859-1", "UTF-8");  ?>
                                                                       
                                                                        <?php 
                                                            			if(strlen($this->escape($team['name'])) > 15) {
                                            						   		echo substr($this->escape($team['name']), 0, 15) . '...';
                                            							} else {
                                            								echo $this->escape($team['name']);
                                            							}
                                                                        ?>
                                                                	</a> 
                                                                </td>
                                                
                                                                <td><?php echo $team->overall['gp'] ?></td>
                                                                <td><?php echo $team->overall['w'] ?></td>
                                                                <td><?php echo $team->overall['d'] ?></td>
                                                                <td><?php echo $team->overall['l'] ?></td>
                                                                <td><?php echo $team->total['gd'] ?></td>
                                                                <td class="last"><?php echo $team->total['p'] ?></td>
                                                          </tr>

											        <?php $i++; } ?>
                    	        <?php } ?>
                    	  
                	<?php } ?>
               	</table>

                <?php } else { ?>
                 	   <center><strong>No Data available here</strong></center>
                 <?php } ?>
			</div> 
                             
                             
            <p class="smatch1">
                    <a class="OrangeLink" href="">See Full Table</a>
             </p>
        </div>
	
   </div><!--/SecondColumn-->

  
  <div class="ThirdColumn"> 

  	  	<div class="featured1">
	  		<p class="mblack">
                        <span class="black"><?php echo $this->compName; ?> Statistics </span>
                       <!--  <span class="sm"><a href="/competitions/showstats/compid/228">More »</a></span>-->
             </p>
             
              <p id="ca2011" class="all">
                    <select id="statsdropdowncompetition">
                        <option value="menu23">&nbsp;Goals Scored</option>
                        <option value="menu24">&nbsp;Yellow Cards</option>
                        <option value="menu25" >&nbsp;Red Cards</option>
                    </select>
               </p>
              
               
               <!--/ Top Scorers  -->
               <div class="cont" id="menu23content" style="display: block;">                                        
					  <div class="scores">                        
					    <ul>                            
					      <li class="name" style="width:120px">Player</li>                            
					      <li class="team" style="width:113px">Team</li>                            
					      <!-- <li class="cont">GP</li>   -->                        
					      <li class="score">                              
					        <img src="<?php echo Zend_Registry::get("contextPath"); ?>/public/images/score1.jpg" alt=""></li>                        
					    </ul>                    
					  </div>   
					   <?php if(sizeOf($this->topscorercomp) != 0) { ?>
						   <?php $i = 1; ?>
		             <?php foreach ($this->topscorercomp as $item) { ?>
		              <?php if($i % 2 == 1) { $style = 'scores1'; }else{ $style = 'scores2';} ?>                                                                                                  
								  <div class="<?php echo $style; ?>">                            
								    <ul>                                
								      <li class="name" style="width:120px">                                                                  
								        <a href="<?php echo $urlGen->getPlayerMasterProfileUrl(null, null, null, $item["player_id"], true ,$item["player_common_name"]); ?>">
								        <?php echo $item['player_name_short']; ?>
								        </a>
								      </li> 
								       <?php if($this->compType == 'club') {  ?>
								          <li class="team" style="background: url('<?php echo Zend_Registry::get("contextPath");?>/utility/imagecrop?w=18&h=18&zc=1&src=<?php echo Zend_Registry::get("contextPath");?><?php echo $this->root_crop;?>/teamlogos/<?php echo $item['team_id']; ?>.gif') no-repeat scroll left center transparent;">
								       	<?php	} else { ?>
								       	  <li class="team" style="background: url('<?php echo Zend_Registry::get("contextPath"); ?>/public/images/flags/<?php echo $item['country_id']; ?>.png') no-repeat scroll left center transparent;">	
								       	<?php	} ?>
		                            
								        <a href="<?php echo $urlGen->getClubMasterProfileUrl($item['team_id'],$item['team_seoname'], True); ?>">
								        	<?php echo $item['team_name']; ?>
								        </a>
								      <li>                                
								                                 
								      <li class="score"><?php echo $item['total_goals']; ?></li>                            
								    </ul>                        
								  </div> 
								<?php $i++; } ?>
							<?php } else { ?>
								<div class ="nodatamodule">No Data Available.</div>
							<?php } ?>
						
				</div> 
				
				<!--/ Yellow Cards -->
				<div class="cont" id="menu24content" style="display: block;">                                        
					  <div class="scores">                        
					    <ul>                            
					      <li class="name" style="width:120px">Player</li>                            
					      <li class="team" style="width:113px">Team</li>                            
					      <!-- <li class="cont">GP</li>   -->                        
					      <li class="score">                              
					        <img src="/public/images/score2.jpg" alt=""></li>                        
					    </ul>                    
					  </div> 
					  <?php if(sizeOf($this->leagueyc) != 0) { ?>  
					   <?php $i = 1; ?>
                    	<?php foreach ($this->leagueyc as $item) { ?>
                   		 <?php if($i % 2 == 1) { $style = 'scores1'; }else{ $style = 'scores2';} ?>                                                                                                  
						  <div class="<?php echo $style; ?>">                            
						    <ul>                                
						      <li class="name" style="width:120px">                                                                  
						        <a href="<?php echo $urlGen->getPlayerMasterProfileUrl(null, null, null, $item["player_id"], true ,$item["player_common_name"]); ?>"><?php echo $item['player_name_short']; ?></a>
						      </li>                                
						      <?php if($this->compType == 'club') {  ?>
						          <li class="team" style="background: url('<?php echo Zend_Registry::get("contextPath");?>/utility/imagecrop?w=18&h=18&zc=1&src=<?php echo Zend_Registry::get("contextPath");?><?php echo $this->root_crop;?>/teamlogos/<?php echo $item['team_id']; ?>.gif') no-repeat scroll left center transparent;">
						       	<?php	} else { ?>
						       	  <li class="team" style="background: url('<?php echo Zend_Registry::get("contextPath"); ?>/public/images/flags/<?php echo $item['country_id']; ?>.png') no-repeat scroll left center transparent;">	
						       	<?php	} ?>                                  
						        	<a href="<?php echo $urlGen->getClubMasterProfileUrl($item['team_id'],$item['team_name'], True); ?>"><?php echo $item['team_name']; ?></a>
						         </li>                                                            
						      <li class="score"><?php echo $item['total_yellow_cards']; ?></li>                            
						    </ul>                        
						  </div> 
						<?php $i++; } ?>
							<?php } else { ?>
								<div class ="nodatamodule">No Data Available.</div>
							<?php } ?>
				</div> 
				
				<!--/ Red Cards  -->
				<div class="cont" id="menu25content" style="display: block;">                                        
					  <div class="scores">                        
					    <ul>                            
					      <li class="name" style="width:120px">Player</li>                            
					      <li class="team" style="width:113px">Team</li>                            
					      <!-- <li class="cont">GP</li>   -->                        
					      <li class="score">                              
					        <img src="/public/images/score3.jpg" alt=""></li>                        
					    </ul>                    
					  </div>   
					  <?php if(sizeOf($this->leagueyc) != 0) { ?>  
					   <?php $i = 1; ?>
                    	<?php foreach ($this->leaguerc as $item) { ?>
                   		 <?php if($i % 2 == 1) { $style = 'scores1'; }else{ $style = 'scores2';} ?>                                                                                                  
						  <div class="<?php echo $style; ?>">                            
						    <ul>                                
						      <li class="name" style="width:120px">                                                                  
						        <a href="<?php echo $urlGen->getPlayerMasterProfileUrl(null, null, null, $item["player_id"], true ,$item["player_common_name"]); ?>"><?php echo $item['player_name_short']; ?></a>
						       </li>                                
						      	<?php if($this->compType == 'club') {  ?>
						          <li class="team" style="background: url('<?php echo Zend_Registry::get("contextPath");?>/utility/imagecrop?w=18&h=18&zc=1&src=<?php echo Zend_Registry::get("contextPath");?><?php echo $this->root_crop;?>/teamlogos/<?php echo $item['team_id']; ?>.gif') no-repeat scroll left center transparent;">
						       	<?php	} else { ?>
						       	  <li class="team" style="background: url('<?php echo Zend_Registry::get("contextPath"); ?>/public/images/flags/<?php echo $item['country_id']; ?>.png') no-repeat scroll left center transparent;">	
						       	<?php	} ?>                                   
						        <a href="<?php echo $urlGen->getClubMasterProfileUrl($item['team_id'],$item['team_name'], True); ?>"><?php echo $item['team_name']; ?></a></li>                                
						     <!-- <li class="cont">7</li>  -->                               
						      <li class="score"><?php echo $item['total_red_cards']; ?></li>                            
						    </ul>                        
						  </div> 
						<?php $i++; } ?>
						<?php } else { ?>
								<div class ="nodatamodule">No Data Available.</div>
						<?php } ?>
				</div>
				<!-- 
				
				<p style="padding-bottom:8px;" class="smatch1">
                    <a href="/competitions/showstats/compid/<?php echo $this->leagueId; ?>/seasonid/<?php echo $this->seasonActive; ?>" class="OrangeLink">See More <?php //echo $this->compName; ?> Statistics</a>
                </p> 
                -->
         </div>
         
              <!-- Scoreboard --> 
          <div id="scoreboard" class="img-shadow">
                    <div class="WrapperForDropShadow">
                        <div class="DropShadowHeader BlueGradientForDropShadowHeader">
                           
                            <h4 class="NoArrowLeft"><?php echo $this->compName; ?> Scores &amp; Schedules </h4>
          
                        </div>
                        <div class="WrapperForScoreTab" id="scoretabBodyId">
                            <ul id="main_tabs" class="TabbedHomeNav">
                                <li id="scoresTabLi"  class="Selected" style="font-size:12px;">
                                    <a id="scoresTab" class="Selected" href="javascript:void(0)">Scores</a>
                                </li>

                                <li id="schedulesTabLi" style="font-size:12px;">
                                    <a id="schedulesTab" href="javascript:void(0)">Schedules</a>
                                </li>
                            </ul>
                            <br class="ClearBoth"/>
                            <div id="ScoresSchedulesWrapperBox">
                                <div id="tab_1" class="tabContent" style="display:none">
                                    
                                    <div class="FeaturedNewsSort" id="ScoresDateFilter">
                                       <a href="#" id="todayms" class="filterSelected">Today</a> |
                                       <a href="javascript:void(0);" id="l3ms">Last 3 Days</a> |
                                       <a href="javascript:void(0);" id="l7ms">Last 7 Days</a> |
								  </div>  
								                
                             	  	<div id="ScorecardContainer">

                                        <div id="scoreboardResult0">

                                            Loading..

                                        </div>
                                    </div>
                                </div>
                                  <div id="tab_2" class="tabContent" style="">
                                    
	                                <div class="FeaturedNewsSort" id="ScoresDateFilter">
	                                   <a href="javascript:void(0);" id="today" class="filterSelected">Today</a> |
	                                   <a href="javascript:void(0);" id="l3">Last 3 Days</a> |
	                                   <a href="javascript:void(0);" id="l7">Last 7 Days</a> |
	                                </div>
	
	                             	<div id="ScorecardContainer">
	                            		<div id="scoreboardResult">
	                            		
	                            			Loading...
	                            		
	                            		</div>    
	                                </div>
                                </div>
                                   <div id="tab_3" class="tabContent" style="display:none;">
                                    <div class="FeaturedNewsSort" id="SchedulesDateFilter">
                                   <a href="javascript:void(0);" id="tomorrow" class="filterSelected">Tomorrow</a> |
                                   <a href="javascript:void(0);" id="n3">Next 3 Days</a> |
                                   <a href="javascript:void(0);" id="n7">Next 7 Days</a> |
								</div>  				 
                           		<div id="ScorecardContainer">
                                      <div id="scoreboardResult2">

                                          Loading..

                                      </div>
                                </div>
                                </div>
                            </div>
                        </div>
                        <div class="SeeMoreNews">
                                    <a class="OrangeLink" href="<?php echo $urlGen->getMainScoresAndMatchesPageUrl(true); ?>" title="">See More &raquo;</a>
                            </div>    
                     </div>
                </div>

        <?php if ($server == 'local') { ?>
        <div id="goalshoutId" class="img-shadow">
            <?php echo $this->partial('scripts/goalshouttemplate.phtml',array('totalGoalShouts'=>$this->totalGoalShouts, 'comments'=>$this->comments ,'elementid'=>$this->leagueId , 'typeofcomment'=>Constants::$_COMMENT_COMPETITION ));?>
        </div>   
        <?php } ?>
        
       
       <!-- Profiles --> 
        <div class="prof">
              <p class="mblack">
               <span class="black">Fan Profiles</span>
               <span class="sm" id="menu6_more">
                   <?php if($session->email == null){ ?>
                   <a href="javascript:loginModal();" title="GoalFace Fan Profiles">More »</a>
              	 <?php }else{?>
              	 <a href="<?php echo Zend_Registry::get("contextPath"); ?>/competitions/showcompetitionfans/id/<?php echo $this->leagueId;?>" title="GoalFace Fan Profiles">More »</a>
              	 <?php }?>
               </span>
              </p>
                         
              <div id="randomprofiles" class="nmatch">
                     
			</div>

           <p class="modfooter">
           <?php if($session->email == null){ ?>
             <a class="orangelink" href="<?php echo Zend_Registry::get("contextPath"); ?>/user/register">See More Fans &raquo;</a>      
           	<?php }else{?>
           	<a class="orangelink" href="<?php echo Zend_Registry::get("contextPath"); ?>/competitions/showcompetitionfans/id/<?php echo $this->leagueId;?>">See More Fans &raquo;</a>
           	<?php }?>
           	</p>
                        
       </div> 

  </div><!--/ThirdColumn-->  
 	
</div>
<script  src="<?php echo Zend_Registry::get("contextPath"); ?>/public/scripts/subscribeteam.js" type="text/javascript"></script>
<script  src="<?php echo Zend_Registry::get("contextPath"); ?>/public/scripts/subscribeplayer.js" type="text/javascript"></script>