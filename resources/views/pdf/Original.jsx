import '../styles/syllabusValidation.css';
import React, { useState, useEffect } from 'react';
import { flushSync } from "react-dom";
import UELogo from "../images/University_of_the_East_seal.png";
import GradingSystem from "../images/GradingSystem.png"
import { Document, Page, Text, View, StyleSheet, Image } from '@react-pdf/renderer';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faDownload } from '@fortawesome/free-solid-svg-icons';
import Navigation from "../components/Navigation";
import axios from "axios";
import { Link, useNavigate, useLocation } from "react-router-dom";
import { useParams } from 'react-router-dom'
//import html2pdf from 'html2pdf.js';
import LearningMatrixWeeks from '../components/LearningMatrixWeeks'
import Swal from 'sweetalert2';

import CollegeConstants from "../components/CollegeConstants";

function SyllabusValidation() {
		const navigate = useNavigate();
		const location = useLocation();

		const axiosInstance = axios.create({
			baseURL: "http://localhost:8000",
			withCredentials: true,
			headers: {
				"Content-type": "application/json",
			},
		});
	
		const [userData, setUserData] = useState(null);
		useEffect(() => {
			const auth = async () => {
				axiosInstance.get('http://localhost:8000/auth/user')
					.then(result => {
						// console.log(result);
						if (result.status === 200) {
							const userInfo = result.data;
							setUserData(userInfo);
						}
					})
					.catch(err => {
						console.log(err);
						navigate("/login")
					});
			}
	
			auth();
			//console.log(location.state.id)
		}, []);
    const { courseCode, version, viewType } = useParams();
	/*
    const [programOutcomesData, setProgramOutcomesData] = useState([
        { text: "", selection: "" },
        { text: "", selection: "" },
        { text: "", selection: "" },
        { text: "", selection: "" },
        { text: "", selection: "" },
        { text: "", selection: "" }
    ]);*/
	const [programOutcomesData, setProgramOutcomesData] = useState({});
    const [syllabusData, setSyllabusData] = useState({});
    const [weeks, setWeeks] = useState({});
    const [referencesData, setReferencesData] = useState({});
    const [otherElements, setOtherElements] = useState({});
    const [preparedBy, setPreparedBy] = useState({});
	const [faculty1, setFaculty1] = useState({});
	const [isLoaded, setIsLoaded] = useState({
		programOutcomes: false,
		syllabusData: false,
		weeks: false,
		references: false,
		otherElements: false,
		preparedBy: false,
		syllabusResult: false
	})
	const [syllabusResult, setSyllabusResult] = useState({});
	useEffect(() => {
		axiosInstance
			.get(
				`http://localhost:8000/syllabusForm/syllabus/getSyllabus/${courseCode}`
			)
			.then((result) => {
				let syllabusResult = result.data;
				flushSync(() => {
					setProgramOutcomesData(syllabusResult.programOutcomes);
					setSyllabusData(syllabusResult.syllabusInfo);
					setWeeks(syllabusResult.weeks);
					setReferencesData(syllabusResult.references);
					setOtherElements(syllabusResult.otherElements);
					setPreparedBy(syllabusResult.preparedBy);
					setSyllabusResult(syllabusResult);
					let commentTextarea = document.getElementById("comment-textarea");
					commentTextarea.value = syllabusResult.status.comment;
				});
			})
			.catch((error) => {
				console.log(error);
			});
	}, []);
    
    // TODO: GET DATA REQUEST
	useEffect(() => {
		if(typeof programOutcomesData === "undefined"){
			console.log("programOutcomesData is undefined");
		}
		else{
			setIsLoaded((prevLoaded) => {
				return {
					...prevLoaded,
					programOutcomes: true,
				};
			});
		}
		if (syllabusData === undefined) {
			console.log("syllabusData is undefined");
		} else {
			setIsLoaded((prevLoaded) => {
				return {
					...prevLoaded,
					syllabusData: true,
				};
			});
		}
		if (weeks === undefined) {
			console.log("weeks is undefined");
		} else {
			setIsLoaded((prevLoaded) => {
				return {
					...prevLoaded,
					weeks: true,
				};
			});
		}
		if (referencesData === undefined) {
			console.log("referencesData is undefined");
		} else {
			setIsLoaded((prevLoaded) => {
				return {
					...prevLoaded,
					references: true,
				};
			});
		}
		if (otherElements === undefined) {
			console.log("otherElements is undefined");
		} else {
			setIsLoaded((prevLoaded) => {
				return {
					...prevLoaded,
					otherElements: true,
				};
			});
		}
		
		if (typeof preparedBy === "undefined") {
			console.log("preparedBy is undefined");
		} else {
			setIsLoaded((prevLoaded) =>{
				return{
					...prevLoaded,
					preparedBy: true,
				};
			});
			setFaculty1(preparedBy.faculty1);
			//console.log(preparedBy);
		}
		if (syllabusResult === undefined) {
			console.log("syllabusResult is undefined");
		} else {
			setIsLoaded((prevLoaded) => {
				return {
					...prevLoaded,
					syllabusResult: true,
				};
			});
			console.log(syllabusResult.unit);
		}
		//console.log(isLoaded);
	}, [
		programOutcomesData,
		syllabusData,
		weeks,
		referencesData,
		otherElements,
		preparedBy,
		syllabusResult
	])
    // TODO: SAVE COMMENT
    const [comment, setComment] = useState("")

    // TODO: MOVE TO APPROVE
    const approveSyllabi = () => {
	
        let titleMsg = comment !== "" ? "Approve publication and disregard comment?" : "Approve publication?";
        Swal.fire({
            title: titleMsg,
            text: `Course code ${courseCode} version ${version}`,
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes"
        }).then((result) => {
            if (result.isConfirmed) {
				let postData = {
					id: JSON.stringify(location.state.id),
					position: JSON.stringify(userData.postion),
					name: JSON.stringify(`${userData.lastname}, ${userData.firstname} ${userData.middlename.charAt(0)}`),
					courseCode: JSON.stringify(courseCode),
					action: JSON.stringify("approve"),
					college: JSON.stringify(syllabusResult.unit),
				};
				axiosInstance.post(`http://localhost:8000/syllabusForm/syllabus/updateStatus?`, { postData })
                Swal.fire({
                    title: "Approved!",
                    text: "Syllabi has been approved.",
                    icon: "success"
                });
            }
        });
    }

    // TODO: REJECT SYLLABI
    const rejectSyllabi = () => {
        // let titleMsg = comment !== "" ? "Approve publication and disregard comment?" : "Approve publication?";

        if (comment == "") {
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "A rejection must include a comment.",
            });
        } else {
            Swal.fire({
                title: "Reject Syllabi",
                text: `Course code ${courseCode} version ${version}`,
                icon: "error",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes"
            }).then((result) => {
                if (result.isConfirmed) {
					let postData = {
						id: JSON.stringify(location.state.id),
						position: JSON.stringify(userData.postion),
						comment: JSON.stringify(comment),
						name: JSON.stringify(`${userData.lastname}, ${userData.firstname} ${userData.middlename.charAt(0)}`),
						courseCode: JSON.stringify(courseCode),
						college: JSON.stringify(syllabusResult.unit),
					};
					axiosInstance.post(
						`http://localhost:8000/syllabusForm/syllabus/reject?`,
						{ postData }
					);
					Swal.fire({
						title: "Approved!",
						text: "Syllabi has been approved.",
						icon: "success",
					});
                    Swal.fire({
                        title: "rejected!",
                        text: "author has been notified.",
                        icon: "success"
                    });
                }
            });
        }


    }
    return (
			<div className="syllabus-validation-body">
				{/* <button onClick={() => {}} className="doc-btn-download">Download PDF</button> */}
				<div id="doc" className="doc">
					<div className="doc-page">
						<img id="ue-logo" src={UELogo} />
						<h1 className="bold align-center">UNIVERSITY OF THE EAST</h1>
						<br />
						<h1 className="bold">UNIVERSITY MISSION STATEMENT:</h1>
						<p className="doc-p">
							&nbsp;&nbsp;&nbsp;&nbsp;Imploring the aid of Divine Providence,
							the University of the East dedicates itself to the service of
							youth, country, and God, and declares adherence to academic
							freedom, progressive instruction, creative scholarship, goodwill
							among nations, and constructive educational leadership.
						</p>
						<br />
						<p className="doc-p">
							&nbsp;&nbsp;&nbsp;&nbsp;Inspired and sustained by a deep sense of
							dedication and a compelling yearning for relevance, the University
							of the East hereby declares as its goal and addresses itself to
							the development of a just, progressive, and humane society.
						</p>
						<br />
						<br />
						<h1 className="bold">UNIVERSITY VISION STATEMENT:</h1>
						<p className="doc-p">
							&nbsp;&nbsp;&nbsp;&nbsp;As a private non-sectarian institution of
							higher learning, the University of the East commits itself to
							producing, through relevant and affordable quality education,
							morally upright and competent leaders in various professions,
							imbued with a strong sense of service to their fellowmen and their
							country.{" "}
						</p>
						<br />
						<br />
						<h1 className="bold">CORE VALUES:</h1>
						<p className="doc-p">
							&nbsp;&nbsp;&nbsp;&nbsp;The University adheres to the core values
							of Excellence, Integrity, Professionalism, Teamwork, Commitment,
							Transparency, Accountability, and Social Responsibility.
						</p>
					</div>
					<div className="doc-page">
						<h1 className="bold">GUIDING PRINCIPLES:</h1>
						<p className="doc-p">
							The Institution declares the following to be its guiding
							principles:
						</p>
						<br />
						<ol className="doc-list-d pad-l-2em" type="1">
							<li>
								Dedication forever to the service of youth, country, and God;
								training the youth to become good and competent citizens;
								promoting a deep and abiding loyalty to the Motherland and her
								own way of life; and serving the will of the Creator;
							</li>
							<li>
								Active encouragement of academic freedom, the only road to the
								realm of wisdom and truth;
							</li>
							<li>
								Constant attunement of curricula to the changing needs of
								individuals and nations in civilizations and cultures
								ceaselessly being enriched by technology, science, and
								scholarship;
							</li>
							<li>
								Encouragement to the utmost of scholarship and research toward
								the broadening of knowledge to new horizons and the augmenting
								of mankind's harvest of freedom, contentment, and abundance;
							</li>
							<li>
								Promotion, through the bonds of culture, of international amity
								and goodwill as basis for the enduring world peace long dreamed
								of by men; and
							</li>
							<li>
								Uttermost endeavor to attain and keep a position at the vanguard
								of higher education so that, as a beacon light to all the
								Orient, it may attract to its campuses promising youth from many
								lands in search of wisdom and truth.
							</li>
						</ol>
						<br />
						<br />
						<h1 className="bold">INSTITUTIONAL OUTCOMES:</h1>
						<p>
							In pursuit of its vision and mission, the University will produce
							GRADUATES
						</p>
						<ol className="doc-list-box pad-l-2em">
							<li>
								attuned to the constantly changing needs and challenges of the
								youth within the context of a proud nation, its enriched culture
								in the global community;
							</li>
							<li>
								able to produce new knowledge gleaned from innovative research –
								the hallmark of an institution’s integrity and dynamism; and
							</li>
							<li>
								capable of rendering relevant and committed service to the
								community, the nation, and the world.
							</li>
						</ol>
					</div>
					{isLoaded.syllabusResult ? 
						<CollegeConstants collegeDepartment={syllabusResult.unit} />
						:
						<></>
					}
					<div className="doc-page">
						<h1 className="bold">PROGRAM OUTCOMES:</h1>
						<table className="tbl-program-outcomes">
							<thead>
								<tr>
									<th className="po-header1" rowSpan={2}>
										Program Outcomes <br />
										<p className="unbold">(Refer to your curriculum map.)</p>
									</th>
									<th className="po-header2" colSpan={3}>
										Programs Outcomes Addressed by the Course <br />
										<p className="unbold">
											(Place a checkmark [ ✓ ] to the appropriate cell.)
										</p>
									</th>
								</tr>
								<tr>
									<th>
										Introduce <br /> (I){" "}
									</th>
									<th>
										Enhanced <br /> (E){" "}
									</th>
									<th>
										Demonstrated <br /> (D){" "}
									</th>
								</tr>
							</thead>
							<tbody>
								{isLoaded["programOutcomes"] ? (
									Object.keys(programOutcomesData).map((key) => (
										<tr key={key}>
											<td>{programOutcomesData[key]["text"]}</td>
											<td>
												{programOutcomesData[key].selection === "Introduced"
													? "✓"
													: "\u00A0"}
											</td>
											<td>
												{programOutcomesData[key].selection === "Enhanced"
													? "✓"
													: "\u00A0"}
											</td>
											<td>
												{programOutcomesData[key].selection === "Demonstrated"
													? "✓"
													: "\u00A0"}
											</td>
										</tr>
									))
								) : (
									<>{isLoaded["programOutcomes"].toString()}</>
								)}
							</tbody>
						</table>
					</div>
					<div className="doc-page">
						<div className="course-sylabus-title-section bold">
							<h1 className="course-syllabus-title">COURSE SYLLABUS IN</h1>
							<h3></h3>
							<h3>Academic Year to </h3>
						</div>
						<table className="course-syllabus-table">
							<tbody>
								<tr>
									<td className="course-syllabus-label bold">Course Code</td>
									<td colSpan={7} className="align-left">
										{isLoaded["syllabusData"] ? syllabusData.courseCode : ""}
									</td>
								</tr>
								<tr>
									<td className="bold">Course title</td>
									<td colSpan={7} className="align-left">
										{isLoaded["syllabusData"] ? syllabusData.courseTitle : ""}
									</td>
								</tr>
								<tr>
									<td className="bold" rowSpan={2}>
										Credit Units
									</td>
									<td>Lecture</td>
									<td colSpan={6} className="align-left">
										{isLoaded["syllabusData"] ? syllabusData.lectureUnits : ""}{" "}
										unit/s
									</td>
								</tr>
								<tr>
									<td>Laboratory/Studio</td>
									<td colSpan={6} className="align-left">
										{isLoaded["syllabusData"] ? syllabusData.labUnits : ""}{" "}
										unit/s
									</td>
								</tr>
								<tr className="row-blue">
									<td className="bold">Course Type</td>
									<td>
										<div className="course-syllabus-checkbox">
											<input
												type="checkbox"
												name="course-type"
												checked={
													isLoaded["syllabusData"]
														? syllabusData.courseType === "Onsite"
														: false
												}
												disabled
											/>{" "}
											<p>
												PURE ONSITE
												<br />
												[Face-to-Face]
											</p>
										</div>
									</td>
									<td>
										<div className="course-syllabus-checkbox">
											<input
												type="checkbox"
												name="course-type"
												checked={
													isLoaded["syllabusData"]
														? syllabusData.courseType === "Offsite"
														: false
												}
												disabled
											/>{" "}
											<p>
												PURE OFFSITE
												<br />
												[Online Distance Learning]
											</p>
										</div>
									</td>
									<td>
										<div className="course-syllabus-checkbox">
											<input
												type="checkbox"
												name="course-type"
												checked={
													isLoaded["syllabusData"]
														? syllabusData.courseType === "Hybrid"
														: false
												}
												disabled
											/>{" "}
											<p>
												HYBRID
												<br />
												[Onsite + Offsite]
											</p>
										</div>
									</td>
									<td>
										<div className="course-syllabus-checkbox">
											<input
												type="checkbox"
												name="course-type"
												checked={
													isLoaded["syllabusData"]
														? syllabusData.courseType === "Others"
														: false
												}
												disabled
											/>{" "}
											<p>Others. Please specify.</p>
										</div>
									</td>
								</tr>
								<tr>
									<td className="bold" rowSpan={2}>
										Pre-requisite(s)
									</td>
									<td>Course Code</td>
									{isLoaded["syllabusData"] &&
									syllabusData["preRequesites"] !== undefined ? (
										syllabusData["preRequesites"].map((preReq) => (
											<td>{preReq.courseCode}</td>
										))
									) : (
										<>{isLoaded["syllabusData"].toString()}</>
									)}
								</tr>
								<tr>
									<td>Course Title</td>
									{isLoaded["syllabusData"] &&
									syllabusData["preRequesites"] !== undefined ? (
										syllabusData["preRequesites"].map((preReq) => (
											<td>{preReq.courseTitle}</td>
										))
									) : (
										<>{isLoaded["syllabusData"].toString()}</>
									)}
								</tr>
							</tbody>
						</table>
						<div className="course-sylabus-course-description-container">
							<h1 className="bold">Course Description: </h1>
							<textarea
								name=""
								id="course-sylabus-course-description-textarea"
								value={
									isLoaded["syllabusData"] ? syllabusData.courseDescription : ""
								}
								disabled
							></textarea>
						</div>

						<h1 className="bold course-sylabus-course-outcomes-title">
							COURSE OUTCOMES
						</h1>
						<div className="course-sylabus-course-outcomes-container">
							<h1>
								Upon completion of the course, the learner will be able to:
							</h1>
							<textarea
								name=""
								id="course-sylabus-course-outcomes-textarea"
								value={
									isLoaded["syllabusData"] ? syllabusData.courseOutcome : ""
								}
								disabled
							></textarea>
						</div>
					</div>
					{weeks !== undefined ? <LearningMatrixWeeks weeks={weeks} /> : <></>}

					{/* REFERENCES*/}
					<div className="doc-page">
						<table className="document-references-table">
							<tbody>
								<tr>
									<th colSpan={2}>REFERENCES</th>
								</tr>

								{isLoaded["references"] ? (
									<>
										<tr>
											<th>Adaptive Digital Solutions</th>
											<td>{referencesData.adaptiveDigitalSolutions}</td>
										</tr>
										<tr>
											<th>Textbook</th>
											<td>{referencesData.textbook}</td>
										</tr>
										<tr>
											<th>Online References</th>
											<td>{referencesData.onlineReferences}</td>
										</tr>
										<tr>
											<th>Others</th>
											<td>{referencesData.otherReferences}</td>
										</tr>
									</>
								) : (
									<></>
								)}
							</tbody>
						</table>
					</div>

					{/* OTHER ELEMENTS */}
					<div className="doc-page">
						<table className="document-other-elements-table">
							<tbody>
								<tr>
									<th colSpan={2}>OTHER ELEMENTS</th>
								</tr>
								{isLoaded["otherElements"] ? (
									<>
										<tr>
											<th>Grading System</th>
											<td>
												Cumulative Grading System is prescribed by the
												University. As such, the following computations are
												applied:
												<img
													className="other-elements-grading-system"
													src={GradingSystem}
													alt="grading system image"
												/>
											</td>
										</tr>
										<tr>
											<th>Classroom Policies</th>
											<td>{otherElements.classroomPolicies}</td>
										</tr>
										<tr>
											<th>Consulting Hours</th>
											<td>{otherElements.consultationHours}</td>
										</tr>
									</>
								) : (
									<></>
								)}
							</tbody>
						</table>
					</div>

					<div className="doc-page">
						<table className="document-peparedy-by-table">
							<tbody>
								<tr>
									<th colSpan={5} style={{color: "white"}}>PREPARED BY:</th>
								</tr>
								<tr>
									<td className="peparedy-spacer" colSpan={5}>
										&nbsp;
									</td>
								</tr>
								<tr>
									<td>
										&nbsp;
										
									</td>
									<td className="peparedy-spacer"></td>
									<td>&nbsp;</td>
									<td className="peparedy-spacer"></td>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td>
										<strong>{
											typeof preparedBy.faculty1 !== "undefined"
												? preparedBy.faculty1.name
												: ""
										}</strong>
									</td>
									<td className="peparedy-spacer"></td>
									<td>
										<strong>{preparedBy.faculty5}</strong>
									</td>
									<td className="peparedy-spacer"></td>
									<td>
										<strong>{preparedBy.faculty3}</strong>
									</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td className="peparedy-spacer"></td>
									<td>&nbsp;</td>
									<td className="peparedy-spacer"></td>
									<td></td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td className="peparedy-spacer"></td>
									<td>&nbsp;</td>
									<td className="peparedy-spacer"></td>
									<td></td>
								</tr>
								<tr>
									<td className="peparedy-spacer" colSpan={5}>
										&nbsp;
									</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td className="peparedy-spacer"></td>
									<td>&nbsp;</td>
									<td className="peparedy-spacer"></td>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td>
										<strong>{preparedBy.faculty4}</strong>
									</td>
									<td className="peparedy-spacer"></td>
									<td>
										<strong>{preparedBy.faculty2}</strong>
									</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td className="peparedy-spacer"></td>
									<td>Member of the Library Committee</td>
									<td className="peparedy-spacer"></td>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td className="peparedy-spacer" colSpan={5}>
										&nbsp;
									</td>
								</tr>
								<tr>
									<td>
										<strong>REVIEWED BY:</strong>
									</td>
									<td className="peparedy-spacer"></td>
									<td>
										<strong>RECOMMENDING APPROVAL:</strong>
									</td>
									<td className="peparedy-spacer"></td>
									<td>
										<strong>APPROVED BY:</strong>
									</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td className="peparedy-spacer"></td>
									<td>&nbsp;</td>
									<td className="peparedy-spacer"></td>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td>
										<strong>{preparedBy.departmentChair}</strong>
									</td>
									<td className="peparedy-spacer"></td>
									<td>
										<strong>{preparedBy.associateDean}</strong>
									</td>
									<td className="peparedy-spacer"></td>
									<td>
										<strong>{preparedBy.dean}</strong>
									</td>
								</tr>
								<tr>
									<td>Department Chair/Program Coordinator</td>
									<td className="peparedy-spacer"></td>
									<td>Associate Dean</td>
									<td className="peparedy-spacer"></td>
									<td>Dean</td>
								</tr>
								<tr>
									<td>Name of Department</td>
									<td className="peparedy-spacer"></td>
									<td>College Initials</td>
									<td className="peparedy-spacer"></td>
									<td>College Name</td>
								</tr>
							</tbody>
						</table>
					</div>
					{viewType == "approval" ? (
						<div className="validation-button">
							<button
								className="btn-approve roboto-regular"
								onClick={() => approveSyllabi()}
							>
								Approve
							</button>
							<button
								className="btn-reject roboto-regular"
								onClick={() => rejectSyllabi()}
							>
								Reject
							</button>
						</div>
					) : (
						<></>
					)}
				</div>

				<div className="comment-section">
					<textarea
						name="comment"
						id="comment-textarea"
						className="ta-comment roboto-regular"
						placeholder="Enter your comments here"
						onChange={(e) => setComment(e.target.value)}
						readOnly={viewType == "approval" ? false : true}
					></textarea>
				</div>
			</div>
		);
}

export default SyllabusValidation
