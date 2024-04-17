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
                <a class="filter-title fw-500 text-dark" data-bs-toggle="collapse" href="#collapseSemploye" role="button" aria-expanded="false">Name or Keyword</a>
                <div class="collapse show" id="collapseSemploye">
                    <div class="main-body">
                        <form action="#" class="input-box position-relative">
                            <input type="text" placeholder="Name or keyword">
                            <button><i class="bi bi-search"></i></button>
                        </form>
                    </div>
                </div>
            </div>
            <!-- /.filter-block -->
            <div class="filter-block bottom-line pb-25 mt-25">
                <a class="filter-title fw-500 text-dark" data-bs-toggle="collapse" href="#collapseCategory" role="button" aria-expanded="false">Category</a>
                <div class="collapse show" id="collapseCategory">
                    <div class="main-body">
                        <select class="nice-select">
                            <option value="0">Web Design</option>
                            <option value="1">Design & Creative </option>
                            <option value="2">It & Development</option>
                            <option value="3">Web & Mobile Dev</option>
                            <option value="4">Writing</option>
                            <option value="5">Sales & Marketing</option>
                        </select>
                    </div>
                </div>
            </div>
            <!-- /.filter-block -->
            <div class="filter-block bottom-line pb-25 mt-25">
                <a class="filter-title fw-500 text-dark" data-bs-toggle="collapse" href="#collapseLocation" role="button" aria-expanded="false">Location</a>
                <div class="collapse show" id="collapseLocation">
                    <div class="main-body">
                        <select class="nice-select bg-white">
                            <option value="0">All Location</option>
                            <option value="1">California, CA</option>
                            <option value="2">New York</option>
                            <option value="3">Miami</option>
                        </select>
                        <div class="loccation-range-select mt-5">
                            <div class="d-flex align-items-center">
                                <span>Radius: &nbsp;</span>
                                <div id="rangeValue">50</div>
                                <span>&nbsp;miles</span>
                            </div>
                            <input type="range" id="locationRange" value="50" max="100">
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.filter-block -->
            <div class="filter-block bottom-line pb-25 mt-25">
                <a class="filter-title fw-500 text-dark collapsed" data-bs-toggle="collapse" href="#collapseExp" role="button" aria-expanded="false">Expert Level</a>
                <div class="collapse" id="collapseExp">
                    <div class="main-body">
                        <ul class="style-none filter-input">
                            <li>
                                <input type="checkbox" name="Experience" value="02">
                                <label>Intermediate</label>
                            </li>
                            <li>
                                <input type="checkbox" name="Experience" value="03">
                                <label>No-Experience</label>
                            </li>
                            <li>
                                <input type="checkbox" name="Experience" value="04">
                                <label>Internship</label>
                            </li>
                            <li>
                                <input type="checkbox" name="Experience" value="05">
                                <label>Expert</label>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /.filter-block -->
            <div class="filter-block bottom-line pb-25 mt-25">
                <a class="filter-title fw-500 text-dark collapsed" data-bs-toggle="collapse" href="#collapseQualification" role="button" aria-expanded="false">Qualification</a>
                <div class="collapse" id="collapseQualification">
                    <div class="main-body">
                        <ul class="style-none filter-input">
                            <li>
                                <input type="checkbox" name="Qualification" value="01">
                                <label>Master’s Degree</label>
                            </li>
                            <li>
                                <input type="checkbox" name="Qualification" value="02">
                                <label>Bachelor Degree</label>
                            </li>
                            <li>
                                <input type="checkbox" name="Qualification" value="03">
                                <label>None</label>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /.filter-block -->
            <div class="filter-block bottom-line pb-25 mt-25">
                <a class="filter-title fw-500 text-dark collapsed" data-bs-toggle="collapse" href="#collapseCType" role="button" aria-expanded="false">Candidate Type</a>
                <div class="collapse" id="collapseCType">
                    <div class="main-body">
                        <ul class="style-none filter-input">
                            <li>
                                <input type="checkbox" name="Gender" value="01">
                                <label>Male</label>
                            </li>
                            <li>
                                <input type="checkbox" name="Gender" value="02">
                                <label>Female</label>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /.filter-block -->

            <div class="filter-block bottom-line pb-25 mt-25">
                <a class="filter-title fw-500 text-dark collapsed" data-bs-toggle="collapse" href="#collapseSalary" role="button" aria-expanded="false">Salary Range</a>
                <div class="collapse" id="collapseSalary">
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
                <a class="filter-title fw-500 text-dark collapsed" data-bs-toggle="collapse" href="#collapseFluency" role="button" aria-expanded="false">English Fluency</a>
                <div class="collapse" id="collapseFluency">
                    <div class="main-body">
                        <select class="nice-select">
                            <option value="0">Basic</option>
                            <option value="1">Conversational</option>
                            <option value="2" selected>Fluent</option>
                            <option value="3">Native/Bilingual</option>
                        </select>
                    </div>
                </div>
            </div>
            <!-- /.filter-block -->

            <a href="#" class="btn-ten fw-500 text-white w-100 text-center tran3s mt-30">Apply Filter</a>
        </div>
    </div>
    <!-- /.filter-area-tab -->
</div>
