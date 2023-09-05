<?php


?>

<div class="col-xl-3 col-lg-4">
    <button type="button" class="filter-btn w-100 pt-2 pb-2 h-auto fw-500 tran3s d-lg-none mb-40" data-bs-toggle="offcanvas" data-bs-target="#filteroffcanvas">
        <i class="bi bi-funnel"></i>
        Filter
    </button>

    <div class="filter-area-tab offcanvas offcanvas-start" id="filteroffcanvas">
        <button type="button" class="btn-close text-reset d-lg-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        <div class="main-title fw-500 text-dark">Filter By</div>
        <div class="light-bg border-20 ps-4 pe-4 pt-25 pb-30 mt-20">
            <div class="filter-block bottom-line pb-25">
                <a class="filter-title fw-500 text-dark" data-bs-toggle="collapse" href="#collapseLocation" role="button" aria-expanded="false">Location</a>
                <div class="collapse show" id="collapseLocation">
                    <div class="main-body">
                        <select class="nice-select bg-white">
                            <option value="0">Washington DC</option>
                            <option value="1">California, CA</option>
                            <option value="2">New York</option>
                            <option value="3">Miami</option>
                        </select>
                    </div>
                </div>
            </div>
            <!-- /.filter-block -->
            <div class="filter-block bottom-line pb-25 mt-25">
                <a class="filter-title fw-500 text-dark" data-bs-toggle="collapse" href="#collapseJobType" role="button" aria-expanded="false">Job Type</a>
                <div class="collapse show" id="collapseJobType">
                    <div class="main-body">
                        <ul class="style-none filter-input">
                            <li>
                                <input type="checkbox" name="JobType" value="01">
                                <label>Fixed-Price <span>7</span></label>
                            </li>
                            <li>
                                <input type="checkbox" name="JobType" value="02">
                                <label>Fulltime <span>3</span></label>
                            </li>
                            <li>
                                <input type="checkbox" name="JobType" value="03">
                                <label>Part-time (20hr/week) <span>0</span></label>
                            </li>
                            <li>
                                <input type="checkbox" name="JobType" value="04">
                                <label>Freelance <span>4</span></label>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /.filter-block -->
            <div class="filter-block bottom-line pb-25 mt-25">
                <a class="filter-title fw-500 text-dark" data-bs-toggle="collapse" href="#collapseExp" role="button" aria-expanded="false">Experience</a>
                <div class="collapse show" id="collapseExp">
                    <div class="main-body">
                        <ul class="style-none filter-input">
                            <li>
                                <input type="checkbox" name="Experience" value="01">
                                <label>Fresher <span>5</span></label>
                            </li>
                            <li>
                                <input type="checkbox" name="Experience" value="02">
                                <label>Intermediate <span>3</span></label>
                            </li>
                            <li>
                                <input type="checkbox" name="Experience" value="03">
                                <label>No-Experience <span>1</span></label>
                            </li>
                            <li>
                                <input type="checkbox" name="Experience" value="04">
                                <label>Internship <span>12</span></label>
                            </li>
                            <li>
                                <input type="checkbox" name="Experience" value="05">
                                <label>Expert <span>17</span></label>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /.filter-block -->
            <div class="filter-block bottom-line pb-25 mt-25">
                <a class="filter-title fw-500 text-dark" data-bs-toggle="collapse" href="#collapseSalary" role="button" aria-expanded="false">Salary</a>
                <div class="collapse show" id="collapseSalary">
                    <div class="main-body">
                        <div class="salary-slider">
                            <div class="price-input d-flex align-items-center pt-5">
                                <div class="field d-flex align-items-center">
                                    <input type="number" class="input-min" value="0" readonly>
                                </div>
                                <div class="pe-1 ps-1">-</div>
                                <div class="field d-flex align-items-center">
                                    <input type="number" class="input-max" value="300" readonly>
                                </div>
                                <div class="currency ps-1">USD</div>
                            </div>
                            <div class="slider">
                                <div class="progress"></div>
                            </div>
                            <div class="range-input mb-10">
                                <input type="range" class="range-min" min="0" max="950" value="0" step="10">
                                <input type="range" class="range-max" min="0" max="1000" value="300" step="10">
                            </div>
                        </div>
                        <ul class="style-none d-flex flex-wrap justify-content-between radio-filter mb-5">
                            <li>
                                <input type="radio" name="jobDuration" value="01">
                                <label>Weekly</label>
                            </li>
                            <li>
                                <input type="radio" name="jobDuration" value="02">
                                <label>Monthly</label>
                            </li>
                            <li>
                                <input type="radio" name="jobDuration" value="03">
                                <label>Hourly</label>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /.filter-block -->
            <div class="filter-block bottom-line pb-25 mt-25">
                <a class="filter-title fw-500 text-dark collapsed" data-bs-toggle="collapse" href="#collapseCategory" role="button" aria-expanded="false">Category</a>
                <div class="collapse" id="collapseCategory">
                    <div class="main-body">
                        <ul class="style-none filter-input">
                            <li>
                                <input type="checkbox" name="Experience" value="01">
                                <label>Web Design <span>15</span></label>
                            </li>
                            <li>
                                <input type="checkbox" name="Experience" value="02">
                                <label>Design & Creative <span>8</span></label>
                            </li>
                            <li>
                                <input type="checkbox" name="Experience" value="03">
                                <label>It & Development <span>7</span></label>
                            </li>
                            <li>
                                <input type="checkbox" name="Experience" value="04">
                                <label>Web & Mobile Dev <span>5</span></label>
                            </li>
                            <li>
                                <input type="checkbox" name="Experience" value="05">
                                <label>Writing <span>4</span></label>
                            </li>
                            <li class="hide">
                                <input type="checkbox" name="Experience" value="06">
                                <label>Sales & Marketing <span>25</span></label>
                            </li>
                            <li class="hide">
                                <input type="checkbox" name="Experience" value="07">
                                <label>Music & Audio <span>1</span></label>
                            </li>
                        </ul>
                        <div class="more-btn"><i class="bi bi-plus"></i> Show More</div>
                    </div>
                </div>
            </div>
            <!-- /.filter-block -->
            <div class="filter-block bottom-line pb-25 mt-25">
                <a class="filter-title fw-500 text-dark collapsed" data-bs-toggle="collapse" href="#collapseTag" role="button" aria-expanded="false">Tags</a>
                <div class="collapse" id="collapseTag">
                    <div class="main-body">
                        <ul class="style-none d-flex flex-wrap justify-space-between radio-filter mb-5">
                            <li>
                                <input type="checkbox" name="tags" value="01">
                                <label>Web Design</label>
                            </li>
                            <li>
                                <input type="checkbox" name="tags" value="02">
                                <label>Squarespace</label>
                            </li>
                            <li>
                                <input type="checkbox" name="tags" value="03">
                                <label>Layout Design</label>
                            </li>
                            <li>
                                <input type="checkbox" name="tags" value="05">
                                <label>Web Development</label>
                            </li>
                            <li>
                                <input type="checkbox" name="tags" value="04">
                                <label>React</label>
                            </li>
                            <li>
                                <input type="checkbox" name="tags" value="06">
                                <label>Full Stack</label>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /.filter-block -->

            <a href="#" class="btn-ten fw-500 text-white w-100 text-center tran3s mt-30">Apply Filter</a>
        </div>
    </div>
    <!-- /.filter-area-tab -->
</div>