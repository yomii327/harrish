<?php
session_start();

include_once("includes/commanfunction.php");
$obj = new COMMAN_Class();

$projectId = $_REQUEST['projectId'];

$q = "select location_id, location_title from project_locations where project_id = '".$_SESSION['idp']."' and location_parent_id = '0' and is_deleted = '0' order by location_title";
$re = mysql_query($q);
while($rw = mysql_fetch_array($re)){	$val[] = $rw;	}?>

<span id="projectId_<?php echo $_SESSION['idp']?>">
<span class="jtree-button demo2" id="projectId_<?php echo $_SESSION['idp']?>" style="background-image: url('images/project.png');background-position: 0 15px;background-repeat: no-repeat;display: block;height: 30px;padding-left: 40px;padding-top: 9px;width: 90%;font-size:26px;cursor: pointer;"><?php echo $obj->getDataByKey('user_projects', 'project_id', $_SESSION['idp'], 'project_name')?></span>
<?php $i=0; if(!empty($val)){foreach($val as $locations){$i++;?>
	<ul class="telefilms" data-cookie="cookie<?php echo $i;?>"><!-- Use 'cookie1' as unique key to save cookie only for this tree -->
		<li id="li_<?php echo $locations['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations['location_id']?>"><?php echo $locations['location_title']?></span>
			<?php $q1 = "select location_id, location_title from project_locations where location_parent_id = '".$locations['location_id']."' and is_deleted = '0' and project_id = '".$_SESSION['idp']."' order by location_title";
				$re1 = mysql_query($q1);
				while($rw1 = mysql_fetch_array($re1)){	$val1[] = $rw1;	}
				if(!empty($val1)){foreach($val1 as $locations1){ ?>
				<ul>
					<li id="li_<?php echo $locations1['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations1['location_id']?>"><?php echo $locations1['location_title']?></span>
						<?php $q2 = "select location_id, location_title from project_locations where location_parent_id = '".$locations1['location_id']."' and is_deleted = '0' and project_id = '".$_SESSION['idp']."' order by location_title";
						$re2 = mysql_query($q2);
						while($rw2 = mysql_fetch_array($re2)){	$val2[] = $rw2;	}
						if(!empty($val2)){foreach($val2 as $locations2){ ?>
						<ul>
							<li id="li_<?php echo $locations2['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations2['location_id']?>"><?php echo $locations2['location_title']?></span>
							
								<?php $q3 = "select location_id, location_title from project_locations where location_parent_id = '".$locations2['location_id']."' and is_deleted = '0' and project_id = '".$_SESSION['idp']."' order by location_title";
								$re3 = mysql_query($q3);
								while($rw3 = mysql_fetch_array($re3)){	$val3[] = $rw3;	}
								if(!empty($val3)){foreach($val3 as $locations3){ ?>
								<ul>
									<li id="li_<?php echo $locations3['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations3['location_id']?>"><?php echo $locations3['location_title']?></span>
										<?php $q4 = "select location_id, location_title from project_locations where location_parent_id = '".$locations3['location_id']."' and is_deleted = '0' and project_id = '".$_SESSION['idp']."' order by location_title";
										$re4 = mysql_query($q4);
										while($rw4 = mysql_fetch_array($re4)){	$val4[] = $rw4;	}
										if(!empty($val4)){foreach($val4 as $locations4){ ?>
										<ul>
											<li id="li_<?php echo $locations4['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations4['location_id']?>" ><?php echo $locations4['location_title']?></span>
												<?php $q5 = "select location_id, location_title from project_locations where location_parent_id = '".$locations4['location_id']."' and is_deleted = '0' and project_id = '".$_SESSION['idp']."' order by location_title";
												$re5 = mysql_query($q5);
												while($rw5 = mysql_fetch_array($re5)){	$val5[] = $rw5;	}
												if(!empty($val5)){foreach($val5 as $locations5){ ?>
												<ul>
													<li id="li_<?php echo $locations5['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations5['location_id']?>" ><?php echo $locations5['location_title']?></span>
														<?php $q6 = "select location_id, location_title from project_locations where location_parent_id = '".$locations5['location_id']."' and is_deleted = '0' and project_id = '".$_SESSION['idp']."' order by location_title";
														$re6 = mysql_query($q6);
														while($rw6 = mysql_fetch_array($re6)){	$val6[] = $rw6;	}
														if(!empty($val6)){foreach($val6 as $locations6){ ?>
														<ul>
															<li id="li_<?php echo $locations6['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations6['location_id']?>" ><?php echo $locations6['location_title']?></span>
																<?php $q7 = "select location_id, location_title from project_locations where location_parent_id = '".$locations6['location_id']."' and is_deleted = '0' and project_id = '".$_SESSION['idp']."' order by location_title";
																$re7 = mysql_query($q7);
																while($rw7 = mysql_fetch_array($re7)){	$val7[] = $rw7;	}
																if(!empty($val7)){foreach($val7 as $locations7){ ?>
																<ul>
																	<li id="li_<?php echo $locations7['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations7['location_id']?>" ><?php echo $locations7['location_title']?></span>
																		<?php $q8 = "select location_id, location_title from project_locations where location_parent_id = '".$locations7['location_id']."' and is_deleted = '0' and project_id = '".$_SESSION['idp']."' order by location_title";
																		$re8 = mysql_query($q8);
																		while($rw8 = mysql_fetch_array($re8)){	$val8[] = $rw8;	}
																		if(!empty($val8)){foreach($val8 as $locations8){ ?>
																		<ul>
																			<li id="li_<?php echo $locations8['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations8['location_id']?>" ><?php echo $locations8['location_title']?></span>
																				<?php $q9 = "select location_id, location_title from project_locations where location_parent_id = '".$locations8['location_id']."' and is_deleted = '0' and project_id = '".$_SESSION['idp']."' order by location_title";
																				$re9 = mysql_query($q9);
																				while($rw9 = mysql_fetch_array($re9)){	$val9[] = $rw9;	}
																				if(!empty($val9)){foreach($val9 as $locations9){ ?>
																				<ul>
																					<li id="li_<?php echo $locations9['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations9['location_id']?>" ><?php echo $locations9['location_title']?></span>
																						<?php $q10 = "select location_id, location_title from project_locations where location_parent_id = '".$locations9['location_id']."' and is_deleted = '0' and project_id = '".$_SESSION['idp']."' order by location_title";
																							$re10 = mysql_query($q10);
																							while($rw10 = mysql_fetch_array($re10)){	$val10[] = $rw10;	}
																							if(!empty($val10)){foreach($val10 as $locations10){ ?>	
																							<ul>
																								<li id="li_<?php echo $locations10['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations10['location_id']?>" ><?php echo $locations10['location_title']?></span>
																									<?php $q11 = "select location_id, location_title from project_locations where location_parent_id = '".$locations10['location_id']."' and is_deleted = '0' and project_id = '".$_SESSION['idp']."' order by location_title";
																									$re11 = mysql_query($q11);
																									while($rw11 = mysql_fetch_array($re11)){	$val11[] = $rw11;	}
																									if(!empty($val11)){foreach($val11 as $locations11){ ?>	
																									<ul>
																										<li id="li_<?php echo $locations11['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations11['location_id']?>" ><?php echo $locations11['location_title']?></span>
																											<?php $q12 = "select location_id, location_title from project_locations where location_parent_id = '".$locations11['location_id']."' and is_deleted = '0' and project_id = '".$_SESSION['idp']."' order by location_title";
																											$re12 = mysql_query($q12);
																											while($rw12 = mysql_fetch_array($re12)){	$val12[] = $rw12;	}
																											if(!empty($val12)){foreach($val12 as $locations12){ ?>	
																											<ul>
																												<li id="li_<?php echo $locations12['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations12['location_id']?>" ><?php echo $locations12['location_title']?></span>
																												<?php $q13 = "select location_id, location_title from project_locations where location_parent_id = '".$locations12['location_id']."' and is_deleted = '0' and project_id = '".$_SESSION['idp']."' order by location_title";
																												$re13 = mysql_query($q13);
																												while($rw13 = mysql_fetch_array($re13)){	$val13[] = $rw13;	}
																												if(!empty($val13)){foreach($val13 as $locations13){ ?>	
																												<ul>
																													<li id="li_<?php echo $locations13['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations13['location_id']?>" ><?php echo $locations13['location_title']?></span>
																													</li>
																												</ul>
																											<?php }$val13 =array();}?>
																											</li>
																										</ul>
																									<?php }$val12 =array();}?>
																									</li>
																								</ul>
																							<?php }$val11 =array();}?>
																							</li>
																						</ul>
																					<?php }$val10 =array();}?>
																					</li>
																				</ul>
																			<?php }$val9 =array();}?>
																			</li>
																		</ul>
																	<?php }$val8 =array();}?>
																	</li>
																</ul>
															<?php }$val7 =array();}?>
															</li>
														</ul>
													<?php }$val6 =array();}?>
													</li>
												</ul>
											<?php }$val5 =array();}?>
											</li>
										</ul>
									<?php }$val4 =array();}?>
									</li>
								</ul>
							<?php }$val3 =array();}?>								
							</li>
						</ul>
					<?php }$val2 =array();}?>
					</li>
				</ul>
			<?php }$val1 =array();}?>
		</li>
	</ul>
<?php }$val=array();}?>
</span>

